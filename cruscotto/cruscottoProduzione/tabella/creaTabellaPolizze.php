<?php

$pezzoLordo = 0;
$pesoLordo = 0;
$pesoPdaOk = 0;
$pesoPdaKo = 0;
$pesoPdaBkl = 0;
$pesoPdaBklp = 0;
$pezzoOk = 0;
$pezzoKo = 0;
$pezzoBkl = 0;
$pezzoBklp = 0;
$resaPezzoLordo = 0;
$resaValoreLordo = 0;
$resaPezzoOk = 0;
$resaValoreOk = 0;
$pesoPostOk = 0;
$pesoPostKo = 0;
$pesoPostBkl = 0;
$pezzoPostOk = 0;
$pezzoPostKo = 0;
$pezzoPostBkl = 0;
$pezzoBollettino = 0;
$pezzoRid = 0;
$percentualeBollettino = 0;
$pezzoCartaceo = 0;
$pezzoMail = 0;
$percentualeInvio = 0;
$pezzoLuce = 0;
$pezzoGas = 0;
$pezzoDual = 0;
$pezzoPolizza = 0;
$ore = 0;

$pezzoBollettinoOk = 0;
$pezzoRidOk = 0;
$percentualeBollettinoOk = 0;
$pezzoCartaceoOk = 0;

$pesoLordoSede = 0;
$pesoPdaOkSede = 0;
$pesoPdaKoSede = 0;
$pesoPdaBklSede = 0;
$pesoPdaBklpSede = 0;
$pezzoLordoSede = 0;
$pezzoOkSede = 0;
$pezzoKoSede = 0;
$pezzoBklSede = 0;
$pezzoBklpSede = 0;
$oreSede = 0;
$pesoPostOkSede = 0;
$pesoPostKoSede = 0;
$pesoPostBklSede = 0;
$pezzoPostOkSede = 0;
$pezzoPostKoSede = 0;
$pezzoPostBklSede = 0;
$pezzoBollettinoSede = 0;
$pezzoRidSede = 0;
$pezzoCartaceoSede = 0;
$pezzoMailSede = 0;
$pezzoLuceSede = 0;
$pezzoGasSede = 0;
$pezzoDualSede = 0;
$pezzoPolizzaSede = 0;
$pezzoBollettinoSedeOk = 0;
$pezzoRidSedeOk = 0;
$pezzoCartaceoSedeOk = 0;
$pezzoMailSedeOk = 0;

$pesoLordoTotalePolizze = 0;
$pesoPdaOkTotalePolizze = 0;
$pesoPdaKoTotalePolizze = 0;
$pesoPdaBklTotalePolizze = 0;
$pesoPdaBklpTotalePolizze = 0;
$pezzoLordoTotalePolizze = 0;
$pezzoOkTotalePolizze = 0;
$pezzoKoTotalePolizze = 0;
$pezzoBklTotalePolizze = 0;
$pezzoBklpTotalePolizze = 0;
$oreTotalePolizze = 0;
$pesoPostOkTotalePolizze = 0;
$pesoPostKoTotalePolizze = 0;
$pesoPostBklTotalePolizze = 0;
$pezzoPostOkTotalePolizze = 0;
$pezzoPostKoTotalePolizze = 0;
$pezzoPostBklTotalePolizze = 0;
$pezzoBollettinoTotalePolizze = 0;
$pezzoRidTotalePolizze = 0;
$pezzoCartaceoTotalePolizze = 0;
$pezzoMailTotalePolizze = 0;
$pezzoLuceTotalePolizze = 0;
$pezzoGasTotalePolizze = 0;
$pezzoDualTotalePolizze = 0;
$pezzoPolizzaTotalePolizze = 0;
$pezzoBollettinoOkTotalePolizze = 0;
$pezzoRidOkTotalePolizze = 0;
$pezzoCartaceoOkTotalePolizze = 0;
$pezzoMailOkTotalePolizze = 0;
$percentualeBollettinoOkTotalePolizze = 0;
$percentualeInvioTotalePolizze = 0;
$percentualeInvioOkTotalePolizze = 0;
$percentualeInvioOkTotale = 0;

$tipoCampagnaEscluso = "";
$queryGroupMandato = "SELECT sede,idMandato,sum(numero)/3600 as ore, mandato.tipoCampagna,lead(sede) over (order by sede,idMandato,tipoCampagna) "
        . "FROM `stringheTotale` inner join mandato on stringheTotale.mandato=mandato.descrizione "
        . "where giorno>='$dataMinore' and giorno<='$dataMaggiore' and livello=1  "
        . "" . $queryMandato . $querySede . "group by sede,idMandato,mandato.tipoCampagna order by sede,idMandato,tipoCampagna";
//echo $queryGroupMandato;
$risultatoQueryGroupMandato = $conn19->query($queryGroupMandato);
$html .= "<br>";
$html .= "<table class='blueTable'>";
include "../../tabella/intestazioneTabella.php";

while ($rigaMandato = $risultatoQueryGroupMandato->fetch_array()) {
    $sede = $rigaMandato[0];
    $sedeRicerca = ucwords($sede);
    $idMandato = $rigaMandato[1];
    $ore = $rigaMandato[2];
    //echo var_dump (floatval($ore));
    $descrizioneMandato = $rigaMandato[3];
    $sedeSuccessiva = $rigaMandato[4];
    //echo $idMandato;
    //echo $sede;
    switch ($idMandato) {
        case "Plenitude":
            $queryCrm = "SELECT "
                    . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                    . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                    . " sum(if(fasePost='OK',if(fasePDA='OK',pezzoLordo,0),0)),sum(if(fasePost='KO',if(fasePDA='OK',pezzoLordo,0),0)), sum(if(fasePost='BKL',if(fasePDA='OK',pezzoLordo,0),0)), "
                    . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                    . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                    . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                    . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0)), "
                    . " sum(if(metodoPagamento='Bollettino Postale',if(fasePDA='OK',pezzoLordo,0),0)),"
                    . " sum(if(metodoPagamento='RID',if(fasePDA='OK',pezzoLordo,0),0)),"
                    . " sum(if(metodoInvio='Cartaceo',if(fasePDA='OK',pezzoLordo,0),0)),"
                    . " sum(if(metodoInvio='Bollettaweb',if(fasePDA='OK',pezzoLordo,0),0)) "
                    . "FROM "
                    . "plenitude "
                    . "inner JOIN aggiuntaPlenitude on plenitude.id=aggiuntaPlenitude.id "
                    . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede' and tipoCampagna='$descrizioneMandato' "
                    . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco' and comodity='Polizza'";

            $queryCrm2 = "SELECT "
                    . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                    . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                    . " sum(if(fasePost='OK',if(fasePDA='OK',pezzoLordo,0),0)),sum(if(fasePost='KO',if(fasePDA='OK',pezzoLordo,0),0)), sum(if(fasePost='BKL',if(fasePDA='OK',pezzoLordo,0),0)), "
                    . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                    . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                    . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                    . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0)),"
                    . " sum(if(metodoPagamento='Bollettino Postale',if(fasePDA='OK',pezzoLordo,0),0)),"
                    . " sum(if(metodoPagamento='RID',if(fasePDA='OK',pezzoLordo,0),0)),"
                    . " sum(if(metodoInvio='Cartaceo',if(fasePDA='OK',pezzoLordo,0),0)),"
                    . " sum(if(metodoInvio='Bollettaweb',if(fasePDA='OK',pezzoLordo,0),0)) "
                    . "FROM "
                    . "plenitude "
                    . "inner JOIN aggiuntaPlenitude on plenitude.id=aggiuntaPlenitude.id "
                    . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede'  "
                    . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco' and comodity='Polizza'";

            break;
    }
    $tipoCampagnaEscluso .= " and tipoCampagna<>'$descrizioneMandato'  ";
    $risultatoCrm = $conn19->query($queryCrm);

    while ($rigaCRM = $risultatoCrm->fetch_array()) {
        $pezzoLordo = round($rigaCRM[5], 0);
        if ($pezzoLordo > 0) {
            $pesoLordo = round($rigaCRM[0], 2);
            $pesoPdaOk = round($rigaCRM[1], 2);
            $pesoPdaKo = round($rigaCRM[2], 2);
            $pesoPdaBkl = round($rigaCRM[3], 2);
            $pesoPdaBklp = round($rigaCRM[4], 2);

            $pezzoOk = round($rigaCRM[6], 0);
            $pezzoKo = round($rigaCRM[7], 0);
            $pezzoBkl = round($rigaCRM[8], 0);
            $pezzoBklp = round($rigaCRM[9], 0);
            $resaPezzoLordo = ($ore == 0) ? 0 : round($pezzoLordo / $ore, 2);
            $resaValoreLordo = ($ore == 0) ? 0 : round($pesoLordo / $ore, 2);
            $resaPezzoOk = ($ore == 0) ? 0 : round($pezzoOk / $ore, 2);
            $resaValoreOk = ($ore == 0) ? 0 : round($pesoPdaOk / $ore, 2);

            $pezzoPostOk = round($rigaCRM[10], 0);
            $pezzoPostKo = round($rigaCRM[11], 0);
            $pezzoPostBkl = round($rigaCRM[12], 0);

            $resaPostOK = ($pezzoOk == 0) ? 0 : round(($pezzoPostOk / $pezzoOk) * 100, 2);
            $resaPostKO = ($pezzoOk == 0) ? 0 : round(($pezzoPostKo / $pezzoOk) * 100, 2);
            $resaPostBkl = ($pezzoOk == 0) ? 0 : round(($pezzoPostBkl / $pezzoOk) * 100, 2);

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
                $percentualeInvio = ($pezzoMail / ($pezzoCartaceo + $pezzoMail)) * 100;
            }
            $pezzoLuce = round($rigaCRM[20], 0);
            $pezzoGas = round($rigaCRM[21], 0);
            $pezzoDual = round($rigaCRM[22], 0);
            $pezzoPolizza = round($rigaCRM[23], 0);

            $pezzoBollettinoOk = round($rigaCRM[24], 0);
            $pezzoRidOk = round($rigaCRM[25], 0);
            if (($pezzoBollettinoOk + $pezzoRidOk) == 0) {
                $percentualeBollettinoOk = 0;
            } else {
                $percentualeBollettinoOk = round((($pezzoBollettinoOk / ($pezzoBollettinoOk + $pezzoRidOk)) * 100), 2);
            }
            $pezzoCartaceoOk = round($rigaCRM[26], 0);
            $pezzoMailOk = round($rigaCRM[27], 0);
            if (($pezzoCartaceoOk + $pezzoMailOk) == 0) {
                $percentualeInvioOk = 0;
            } else {
                $percentualeInvioOk = ($pezzoMailOk / ($pezzoCartaceoOk + $pezzoMailOk)) * 100;
            }

            $html .= "<tr>";
            $html .= "<td style='background-color: pink'>$idMandato</td>";
            $html .= "<td style='background-color: pink'>$sede</td>";

            $html .= "<td style='background-color: pink'>$descrizioneMandato</td>";

            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoLordo</td>";
            $html .= "<td>$pesoLordo</td>";

            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoOk</td>";
            $html .= "<td>$pesoPdaOk</td>";

            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoKo</td>";
            $html .= "<td>$pesoPdaKo</td>";

            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBkl</td>";
            $html .= "<td>$pesoPdaBkl</td>";

            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBklp</td>";
            $html .= "<td>$pesoPdaBklp</td>";

            $html .= "<td style='border-left: 5px double #D0E4F5'>$ore</td>";

            $html .= "<td style='border-left: 5px double #D0E4F5'>$resaPezzoLordo</td>";
            $html .= "<td>$resaValoreLordo</td>";

            $html .= "<td style='border-left: 5px double #D0E4F5'>$resaPezzoOk</td>";
            $html .= "<td>$resaValoreOk</td>";

            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBollettino</td>";
            $html .= "<td>$pezzoRid</td>";
            $html .= "<td>$percentualeBollettino</td>";

            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBollettinoOk</td>";
            $html .= "<td>$pezzoRidOk</td>";
            $html .= "<td>$percentualeBollettinoOk</td>";

            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoCartaceo</td>";
            $html .= "<td>$pezzoMail</td>";
            $html .= "<td>$percentualeInvio</td>";

            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoCartaceoOk</td>";
            $html .= "<td>$pezzoMailOk</td>";
            $html .= "<td>$percentualeInvioOk</td>";

            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoLuce</td>";
            $html .= "<td>$pezzoGas</td>";
            $html .= "<td>$pezzoDual</td>";
            $html .= "<td>$pezzoPolizza</td>";

            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostOk</td>";
            $html .= "<td>$resaPostOK</td>";

            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostKo</td>";
            $html .= "<td>$resaPostKO</td>";

            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostBkl</td>";
            $html .= "<td>$resaPostBkl</td>";

            $html .= "</tr>";
        } else {
            $ore = 0;
        }

        if ($sede <> $sedeSuccessiva) {
            if ($sedeSuccessiva == null) {
                if ($testMode == "true") {
                    $pesoLordoSede += $pesoLordo;
                    $pesoPdaOkSede += $pesoPdaOk;
                    $pesoPdaKoSede += $pesoPdaKo;
                    $pesoPdaBklSede += $pesoPdaBkl;
                    $pesoPdaBklpSede += $pesoPdaBklp;
                    $pezzoLordoSede += $pezzoLordo;
                    $pezzoOkSede += $pezzoOk;
                    $pezzoKoSede += $pezzoKo;
                    $pezzoBklSede += $pezzoBkl;
                    $pezzoBklpSede += $pezzoBklp;
                    //$oreSede += $ore;

                    $pezzoPostOkSede += $pezzoPostOk;
                    $pezzoPostKoSede += $pezzoPostKo;
                    $pezzoPostBklSede += $pezzoPostBkl;
                    $pezzoBollettinoSede += $pezzoBollettino;
                    $pezzoRidSede += $pezzoRid;
                    $pezzoCartaceoSede += $pezzoCartaceo;
                    $pezzoMailSede += $pezzoMail;
                    $pezzoLuceSede += $pezzoLuce;
                    $pezzoGasSede += $pezzoGas;
                    $pezzoDualSede += $pezzoDual;
                    $pezzoPolizzaSede += $pezzoPolizza;

                    $pezzoBollettinoOkSede += $pezzoBollettinoOk;
                    $pezzoRidOkSede += $pezzoRidOk;
                    $pezzoCartaceoOkSede += $pezzoCartaceoOk;
                    $pezzoMailOkSede += $pezzoMailOk;

                    $pezzoLordo = 0;
                    $pesoLordo = 0;
                    $pesoPdaOk = 0;
                    $pesoPdaKo = 0;
                    $pesoPdaBkl = 0;
                    $pesoPdaBklp = 0;
                    $pezzoOk = 0;
                    $pezzoKo = 0;
                    $pezzoBkl = 0;
                    $pezzoBklp = 0;
                    $resaPezzoLordo = 0;
                    $resaValoreLordo = 0;
                    $resaPezzoOk = 0;
                    $resaValoreOk = 0;
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
                    $ore = 0;
                    $pezzoBollettinoOk = 0;
                    $pezzoRidOk = 0;

                    $pezzoCartaceoOk = 0;
                    $pezzoMailOk = 0;

                    $query = $queryCrm2 . $tipoCampagnaEscluso;
                    $risultatoCRM2 = $conn19->query($query);
                    $conteggio = $risultatoCRM2->num_rows;
                    //echo $query;
                    if ($conteggio > 0) {

                        $rigaCRM2 = $risultatoCRM2->fetch_array();
                        $pezzoLordo = round($rigaCRM2[5], 0);
                        if ($pezzoLordo > 0) {
                            $pesoLordo = round($rigaCRM2[0], 2);
                            $pesoPdaOk = round($rigaCRM2[1], 2);
                            $pesoPdaKo = round($rigaCRM2[2], 2);
                            $pesoPdaBkl = round($rigaCRM2[3], 2);
                            $pesoPdaBklp = round($rigaCRM2[4], 2);

                            $pezzoOk = round($rigaCRM2[6], 0);
                            $pezzoKo = round($rigaCRM2[7], 0);
                            $pezzoBkl = round($rigaCRM2[8], 0);
                            $pezzoBklp = round($rigaCRM2[9], 0);
                            $resaPezzoLordo = ($ore == 0) ? 0 : round($pezzoLordo / $ore, 2);
                            $resaValoreLordo = ($ore == 0) ? 0 : round($pesoLordo / $ore, 2);
                            $resaPezzoOk = ($ore == 0) ? 0 : round($pezzoOk / $ore, 2);
                            $resaValoreOk = ($ore == 0) ? 0 : round($pesoPdaOk / $ore, 2);

                            $pezzoPostOk = round($rigaCRM2[13], 0);
                            $pezzoPostKo = round($rigaCRM2[14], 0);
                            $pezzoPostBkl = round($rigaCRM2[15], 0);
                            $resaPostOK = ($pezzoOk == 0) ? 0 : round(($pezzoPostOk / $pezzoOk) * 100, 2);
                            $resaPostKO = ($pezzoOk == 0) ? 0 : round(($pezzoPostKo / $pezzoOk) * 100, 2);
                            $resaPostBkl = ($pezzoOk == 0) ? 0 : round(($pezzoPostBkl / $pezzoOk) * 100, 2);

                            $pezzoBollettino = round($rigaCRM2[16], 0);
                            $pezzoRid = round($rigaCRM2[17], 0);
                            if (($pezzoBollettino + $pezzoRid) == 0) {
                                $percentualeBollettino = 0;
                            } else {
                                $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                            }
                            $pezzoCartaceo = round($rigaCRM2[18], 0);
                            $pezzoMail = round($rigaCRM2[19], 0);
                            if (($pezzoCartaceo + $pezzoMail) == 0) {
                                $percentualeInvio = 0;
                            } else {
                                $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                            }
                            $pezzoLuce = round($rigaCRM2[20], 0);
                            $pezzoGas = round($rigaCRM2[21], 0);
                            $pezzoDual = round($rigaCRM2[22], 0);
                            $pezzoPolizza = round($rigaCRM2[23], 0);

                            $pezzoBollettinoOk = round($rigaCRM[24], 0);
                            $pezzoRidOk = round($rigaCRM[25], 0);
                            if (($pezzoBollettinoOk + $pezzoRidOk) == 0) {
                                $percentualeBollettinoOk = 0;
                            } else {
                                $percentualeBollettinoOk = round((($pezzoBollettinoOk / ($pezzoBollettinoOk + $pezzoRidOk)) * 100), 2);
                            }
                            $pezzoCartaceoOk = round($rigaCRM[26], 0);
                            $pezzoMailOk = round($rigaCRM[27], 0);
                            if (($pezzoCartaceoOk + $pezzoMailOk) == 0) {
                                $percentualeInvioOk = 0;
                            } else {
                                $percentualeInvioOk = ($pezzoMailOk / ($pezzoCartaceoOk + $pezzoMailOk)) * 100;
                            }


                            $html .= "<tr>";
                            $html .= "<td style='background-color: pink'>$idMandato</td>";
                            $html .= "<td style='background-color: pink'>$sede</td>";

                            $html .= "<td style='background-color: pink'>ESClUSO</td>";

                            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoLordo</td>";
                            $html .= "<td>$pesoLordo</td>";

                            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoOk</td>";
                            $html .= "<td>$pesoPdaOk</td>";

                            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoKo</td>";
                            $html .= "<td>$pesoPdaKo</td>";

                            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBkl</td>";
                            $html .= "<td>$pesoPdaBkl</td>";

                            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBklp</td>";
                            $html .= "<td>$pesoPdaBklp</td>";

                            $html .= "<td style='border-left: 5px double #D0E4F5'>-</td>";

                            $html .= "<td style='border-left: 5px double #D0E4F5'>$resaPezzoLordo</td>";
                            $html .= "<td>$resaValoreLordo</td>";

                            $html .= "<td style='border-left: 5px double #D0E4F5'>$resaPezzoOk</td>";
                            $html .= "<td>$resaValoreOk</td>";

                            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBollettino</td>";
                            $html .= "<td>$pezzoRid</td>";
                            $html .= "<td>$percentualeBollettino</td>";

                            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBollettinoOk</td>";
                            $html .= "<td>$pezzoRidOk</td>";
                            $html .= "<td>$percentualeBollettinoOk</td>";

                            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoCartaceoOk</td>";
                            $html .= "<td>$pezzoMailOk</td>";
                            $html .= "<td>$percentualeInvioOk</td>";

                            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoCartaceo</td>";
                            $html .= "<td>$pezzoMail</td>";
                            $html .= "<td>$percentualeInvio</td>";

                            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoLuce</td>";
                            $html .= "<td>$pezzoGas</td>";
                            $html .= "<td>$pezzoDual</td>";
                            $html .= "<td>$pezzoPolizza</td>";

                            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostOk</td>";
                            $html .= "<td>$resaPostOK</td>";

                            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostKo</td>";
                            $html .= "<td>$resaPostKO</td>";

                            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostBkl</td>";
                            $html .= "<td>$resaPostBkl</td>";

                            $html .= "</tr>";
                        }
                    }
                }
                $pesoLordoSede += $pesoLordo;
                $pesoPdaOkSede += $pesoPdaOk;
                $pesoPdaKoSede += $pesoPdaKo;
                $pesoPdaBklSede += $pesoPdaBkl;
                $pesoPdaBklpSede += $pesoPdaBklp;
                $pezzoLordoSede += $pezzoLordo;
                $pezzoOkSede += $pezzoOk;
                $pezzoKoSede += $pezzoKo;
                $pezzoBklSede += $pezzoBkl;
                $pezzoBklpSede += $pezzoBklp;
                $oreSede += $ore;

                $pezzoPostOkSede += $pezzoPostOk;
                $pezzoPostKoSede += $pezzoPostKo;
                $pezzoPostBklSede += $pezzoPostBkl;

                $resaPostOKSede = ($pezzoOkSede == 0) ? 0 : round(($pezzoPostOkSede / $pezzoOkSede) * 100, 2);
                $resaPostKOSede = ($pezzoOkSede == 0) ? 0 : round(($pezzoPostKoSede / $pezzoOkSede) * 100, 2);
                $resaPostBklSede = ($pezzoOkSede == 0) ? 0 : round(($pezzoPostBklSede / $pezzoOkSede) * 100, 2);

                $pezzoBollettinoSede += $pezzoBollettino;
                $pezzoRidSede += $pezzoRid;
                $pezzoCartaceoSede += $pezzoCartaceo;
                $pezzoMailSede += $pezzoMail;
                $pezzoLuceSede += $pezzoLuce;
                $pezzoGasSede += $pezzoGas;
                $pezzoDualSede += $pezzoDual;
                $pezzoPolizzaSede += $pezzoPolizza;
                $resaPezzoLordoSede = ($oreSede == 0) ? 0 : round($pezzoLordoSede / $oreSede, 2);
                $resaValoreLordoSede = ($oreSede == 0) ? 0 : round($pesoLordoSede / $oreSede, 2);
                $resaPezzoOkSede = ($oreSede == 0) ? 0 : round($pezzoOkSede / $oreSede, 2);
                $resaValoreOkSede = ($oreSede == 0) ? 0 : round($pesoPdaOkSede / $oreSede, 2);
                if (($pezzoBollettinoSede + $pezzoRidSede) == 0) {
                    $percentualeBollettinoSede = 0;
                } else {
                    $percentualeBollettinoSede = round((($pezzoBollettinoSede / ($pezzoBollettinoSede + $pezzoRidSede)) * 100), 2);
                }
                if (($pezzoCartaceoSede + $pezzoMailSede) == 0) {
                    $percentualeInvioSede = 0;
                } else {
                    $percentualeInvioSede = ($pezzoMailSede / ($pezzoCartaceoSede + $pezzoMailSede)) * 100;
                }

                $pezzoBollettinoOkSede += $pezzoBollettinoOk;
                $pezzoRidOkSede += $pezzoRidOk;
                $pezzoCartaceoOkSede += $pezzoCartaceoOk;
                $pezzoMailOkSede += $pezzoMailOk;
                if (($pezzoBollettinoOkSede + $pezzoRidOkSede) == 0) {
                    $percentualeBollettinoOkSede = 0;
                } else {
                    $percentualeBollettinoOkSede = round((($pezzoBollettinoOkSede / ($pezzoBollettinoOkSede + $pezzoRidOkSede)) * 100), 2);
                }
                if (($pezzoCartaceoOkSede + $pezzoMailOkSede) == 0) {
                    $percentualeInvioOkSede = 0;
                } else {
                    $percentualeInvioOkSede = ($pezzoMailOkSede / ($pezzoCartaceoOkSede + $pezzoMailOkSede)) * 100;
                }

                $pezzoLordo = 0;
                $pesoLordo = 0;
                $pesoPdaOk = 0;
                $pesoPdaKo = 0;
                $pesoPdaBkl = 0;
                $pesoPdaBklp = 0;
                $pezzoOk = 0;
                $pezzoKo = 0;
                $pezzoBkl = 0;
                $pezzoBklp = 0;
                $resaPezzoLordo = 0;
                $resaValoreLordo = 0;
                $resaPezzoOk = 0;
                $resaValoreOk = 0;
                $pesoPostOk = 0;
                $pesoPostKo = 0;
                $pesoPostBkl = 0;
                $pezzoPostOk = 0;
                $pezzoPostKo = 0;
                $pezzoPostBkl = 0;
                $pezzoBollettino = 0;
                $pezzoRid = 0;
                $percentualeBollettino = 0;
                $pezzoCartaceo = 0;
                $pezzoMail = 0;
                $percentualeInvio = 0;
                $pezzoLuce = 0;
                $pezzoGas = 0;
                $pezzoDual = 0;
                $pezzoPolizza = 0;
                $ore = 0;

                $pezzoBollettinoOk = 0;
                $pezzoRidOk = 0;
                $percentualeBollettinoOk = 0;
                $pezzoCartaceoOk = 0;

                $html .= "<tr style='background-color: orange'>";
                $html .= "<td colspan='3'>$sede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoLordoSede</td>";
                $html .= "<td>$pesoLordoSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoOkSede</td>";
                $html .= "<td>$pesoPdaOkSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoKoSede</td>";
                $html .= "<td>$pesoPdaKoSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBklSede</td>";
                $html .= "<td>$pesoPdaBklSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBklpSede</td>";
                $html .= "<td>$pesoPdaBklpSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$oreSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$resaPezzoLordoSede</td>";
                $html .= "<td>$resaValoreLordoSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$resaPezzoOkSede</td>";
                $html .= "<td>$resaValoreOkSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBollettinoSede</td>";
                $html .= "<td>$pezzoRidSede</td>";
                $html .= "<td>$percentualeBollettinoSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBollettinoOkSede</td>";
                $html .= "<td>$pezzoRidOkSede</td>";
                $html .= "<td>$percentualeBollettinoOkSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoCartaceoSede</td>";
                $html .= "<td>$pezzoMailSede</td>";
                $html .= "<td>$percentualeInvioSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoCartaceoOkSede</td>";
                $html .= "<td>$pezzoMailOkSede</td>";
                $html .= "<td>$percentualeInvioOkSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoLuceSede</td>";
                $html .= "<td>$pezzoGasSede</td>";
                $html .= "<td>$pezzoDualSede</td>";
                $html .= "<td>$pezzoPolizzaSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostOkSede</td>";
                $html .= "<td>$resaPostOKSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostKoSede</td>";
                $html .= "<td>$resaPostKOSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostBklSede</td>";
                $html .= "<td>$resaPostBklSede</td>";

                $html .= "</tr>";

                $tipoCampagnaEscluso = "";
                $pesoLordoTotalePolizze += $pesoLordoSede;
                $pesoPdaOkTotalePolizze += $pesoPdaOkSede;
                $pesoPdaKoTotalePolizze += $pesoPdaKoSede;
                $pesoPdaBklTotalePolizze += $pesoPdaBklSede;
                $pesoPdaBklpTotalePolizze += $pesoPdaBklpSede;
                $pezzoLordoTotalePolizze += $pezzoLordoSede;
                $pezzoOkTotalePolizze += $pezzoOkSede;
                $pezzoKoTotalePolizze += $pezzoKoSede;
                $pezzoBklTotalePolizze += $pezzoBklSede;
                $pezzoBklpTotalePolizze += $pezzoBklpSede;
                $oreTotalePolizze += $oreSede;

                $pezzoPostOkTotalePolizze += $pezzoPostOkSede;
                $pezzoPostKoTotalePolizze += $pezzoPostKoSede;
                $pezzoPostBklTotalePolizze += $pezzoPostBklSede;

                $resaPostOKTotalePolizze = ($pezzoOkTotalePolizze == 0) ? 0 : round(($pezzoPostOkTotalePolizze / $pezzoOkTotalePolizze) * 100, 2);
                $resaPostKOTotalePolizze = ($pezzoOkTotalePolizze == 0) ? 0 : round(($pezzoPostKoTotalePolizze / $pezzoOkTotalePolizze) * 100, 2);
                $resaPostBklTotalePolizze = ($pezzoOkTotalePolizze == 0) ? 0 : round(($pezzoPostBklTotalePolizze / $pezzoOkTotalePolizze) * 100, 2);

                $pezzoBollettinoTotalePolizze += $pezzoBollettinoSede;
                $pezzoRidTotalePolizze += $pezzoRidSede;
                $pezzoCartaceoTotalePolizze += $pezzoCartaceoSede;
                $pezzoMailTotalePolizze += $pezzoMailSede;
                $pezzoLuceTotalePolizze += $pezzoLuceSede;
                $pezzoGasTotalePolizze += $pezzoGasSede;
                $pezzoDualTotalePolizze += $pezzoDualSede;
                $pezzoPolizzaTotalePolizze += $pezzoPolizzaSede;

                $resaPezzoLordoTotalePolizze = ($oreTotalePolizze == 0) ? 0 : round($pezzoLordoTotalePolizze / $oreTotalePolizze, 2);
                $resaValoreLordoTotalePolizze = ($oreTotalePolizze == 0) ? 0 : round($pesoLordoTotalePolizze / $oreTotalePolizze, 2);
                $resaPezzoOkTotalePolizze = ($oreTotalePolizze == 0) ? 0 : round($pezzoOkTotalePolizze / $oreTotalePolizze, 2);
                $resaValoreOkTotalePolizze = ($oreTotalePolizze == 0) ? 0 : round($pesoPdaOkTotalePolizze / $oreTotalePolizze, 2);

                if (($pezzoBollettinoTotalePolizze + $pezzoRidTotalePolizze) == 0) {
                    $percentualeBollettinoTotalePolizze = 0;
                } else {
                    $percentualeBollettinoTotalePolizze = round((($pezzoBollettinoTotalePolizze / ($pezzoBollettinoTotalePolizze + $pezzoRidTotalePolizze)) * 100), 2);
                }
                if (($pezzoCartaceoTotalePolizze + $pezzoMailTotalePolizze) == 0) {
                    $percentualeInvioTotalePolizze = 0;
                } else {
                    $percentualeInvioTotalePolizze = ($pezzoCartaceoTotalePolizze / ($pezzoCartaceoTotalePolizze + $pezzoMailTotalePolizze)) * 100;
                }
                $pezzoBollettinoOkTotalePolizze += $pezzoBollettinoOkSede;
                $pezzoRidOkTotalePolizze += $pezzoRidOkSede;
                $pezzoCartaceoOkTotalePolizze += $pezzoCartaceoOkSede;
                $pezzoMailOkTotalePolizze += $pezzoMailOkSede;
                if (($pezzoBollettinoOkTotalePolizze + $pezzoRidOkTotalePolizze) == 0) {
                    $percentualeBollettinoOkTotale = 0;
                } else {
                    $percentualeBollettinoOkTotale = round((($pezzoBollettinoOkTotalePolizze / ($pezzoBollettinoOkTotalePolizze + $pezzoRidOkTotalePolizze)) * 100), 2);
                }
                if (($pezzoCartaceoOkTotalePolizze + $pezzoMailOkTotalePolizze) == 0) {
                    $percentualeInvioOkTotale = 0;
                } else {
                    $percentualeInvioOkTotale = ($pezzoMailOkTotalePolizze / ($pezzoCartaceoOkTotalePolizze + $pezzoMailOkTotalePolizze)) * 100;
                }



                $html .= "<tr style='background-color: orangered'>";
                $html .= "<td colspan='3'>Totale Polizze</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoLordoTotalePolizze</td>";
                $html .= "<td>$pesoLordoTotalePolizze</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoOkTotalePolizze</td>";
                $html .= "<td>$pesoPdaOkTotalePolizze</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoKoTotalePolizze</td>";
                $html .= "<td>$pesoPdaKoTotalePolizze</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBklTotalePolizze</td>";
                $html .= "<td>$pesoPdaBklTotalePolizze</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBklpTotalePolizze</td>";
                $html .= "<td>$pesoPdaBklpTotalePolizze</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$oreTotalePolizze</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$resaPezzoLordoTotalePolizze</td>";
                $html .= "<td>$resaValoreLordoTotalePolizze</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$resaPezzoOkTotalePolizze</td>";
                $html .= "<td>$resaValoreOkTotalePolizze</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBollettinoTotalePolizze</td>";
                $html .= "<td>$pezzoRidTotalePolizze</td>";
                $html .= "<td>$percentualeBollettinoTotalePolizze</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBollettinoOkTotalePolizze</td>";
                $html .= "<td>$pezzoRidOkTotalePolizze</td>";
                $html .= "<td>$percentualeBollettinoOkTotalePolizze</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoCartaceoTotalePolizze</td>";
                $html .= "<td>$pezzoMailTotalePolizze</td>";
                $html .= "<td>$percentualeInvioTotalePolizze</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoCartaceoOkTotalePolizze</td>";
                $html .= "<td>$pezzoMailOkTotalePolizze</td>";
                $html .= "<td>$percentualeInvioOkTotalePolizze</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoLuceTotalePolizze</td>";
                $html .= "<td>$pezzoGasTotalePolizze</td>";
                $html .= "<td>$pezzoDualTotalePolizze</td>";
                $html .= "<td>$pezzoPolizzaTotalePolizze</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostOkTotalePolizze</td>";
                $html .= "<td>$resaPostOKTotalePolizze</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostKoTotalePolizze</td>";
                $html .= "<td>$resaPostKOTotalePolizze</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostBklTotalePolizze</td>";
                $html .= "<td>$resaPostBklTotalePolizze</td>";

                $html .= "</tr>";

                $html .= "<tr style='background-color: SlateBlue'>";
                $html .= "<td colspan='3'>Rapporto Polizze/Totale</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>" . (($pezzoLordoTotale == 0) ? 0 : round($pezzoLordoTotalePolizze / $pezzoLordoTotale, 2)) . "</td>";
                $html .= "<td>-</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>" . (($pezzoOkTotale == 0) ? 0 : round($pezzoOkTotalePolizze / $pezzoOkTotale, 2)) . "</td>";
                $html .= "<td>$pesoPdaOkTotalePolizze</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>" . (($pezzoKoTotale == 0) ? 0 : round($pezzoKoTotalePolizze / $pezzoKoTotale, 2)) . "</td>";
                $html .= "<td>-</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>" . (($pezzoBklTotale == 0) ? 0 : round($pezzoBklTotalePolizze / $pezzoBklTotale, 2)) . "</td>";
                $html .= "<td>-</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>" . (($pezzoBklpTotale == 0) ? 0 : round($pezzoBklpTotalePolizze / $pezzoBklpTotale, 2)) . "</td>";
                $html .= "<td>-</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>-</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>-</td>";
                $html .= "<td>-</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>-</td>";
                $html .= "<td>-</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>" . (($pezzoBollettinoTotale == 0) ? 0 : round($pezzoBollettinoTotalePolizze / $pezzoBollettinoTotale, 2)) . "</td>";
                $html .= "<td>" . (($pezzoRidTotale == 0) ? 0 : round($pezzoRidTotalePolizze / $pezzoRidTotale, 2)) . "</td>";
                $html .= "<td>" . (($percentualeBollettinoTotale == 0) ? 0 : round($percentualeBollettinoTotalePolizze / $percentualeBollettinoTotale, 2)) . "</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>" . (($pezzoBollettinoOkTotale == 0) ? 0 : round($pezzoBollettinoOkTotalePolizze / $pezzoBollettinoOkTotale, 2)) . "</td>";
                $html .= "<td>" . (($pezzoRidOkTotale == 0) ? 0 : round($pezzoRidOkTotalePolizze / $pezzoRidOkTotale, 2)) . "</td>";
                $html .= "<td>" . (($percentualeBollettinoOkTotale == 0) ? 0 : round($percentualeBollettinoOkTotalePolizze / $percentualeBollettinoOkTotale, 2)) . "</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>" . (($pezzoCartaceoTotale == 0) ? 0 : round($pezzoCartaceoTotalePolizze / $pezzoCartaceoTotale, 2)) . "</td>";
                $html .= "<td>" . (($pezzoMailTotale == 0) ? 0 : round($pezzoMailTotalePolizze / $pezzoMailTotale, 2)) . "</td>";
                $html .= "<td>" . (($percentualeInvioTotale == 0) ? 0 : round($percentualeInvioTotalePolizze / $percentualeInvioTotale, 2)) . "</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>" . (($pezzoCartaceoOkTotale == 0) ? 0 : round($pezzoCartaceoOkTotalePolizze / $pezzoCartaceoOkTotale, 2)) . "</td>";
                $html .= "<td>" . (($pezzoMailOkTotale == 0) ? 0 : round($pezzoMailOkTotalePolizze / $pezzoMailOkTotale, 2)) . "</td>";
                $html .= "<td>" . (($percentualeInvioOkTotale == 0) ? 0 : round($percentualeInvioOkTotalePolizze / $percentualeInvioOkTotale, 2)) . "</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>-</td>";
                $html .= "<td>-</td>";
                $html .= "<td>-</td>";
                $html .= "<td>-</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>-</td>";
                $html .= "<td>-</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>-</td>";
                $html .= "<td>-</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>-</td>";
                $html .= "<td>-</td>";

                $html .= "</tr>";
            } else {
//                if ($testMode == "true") {
//                    $pesoLordoSede += $pesoLordo;
//                    $pesoPdaOkSede += $pesoPdaOk;
//                    $pesoPdaKoSede += $pesoPdaKo;
//                    $pesoPdaBklSede += $pesoPdaBkl;
//                    $pesoPdaBklpSede += $pesoPdaBklp;
//                    $pezzoLordoSede += $pezzoLordo;
//                    $pezzoOkSede += $pezzoOk;
//                    $pezzoKoSede += $pezzoKo;
//                    $pezzoBklSede += $pezzoBkl;
//                    $pezzoBklpSede += $pezzoBklp;
//                    //$oreSede += $ore;
//                    $pesoPostOkSede += $pesoPostOk;
//                    $pesoPostKoSede += $pesoPostKo;
//                    $pesoPostBklSede += $pesoPostBkl;
//                    $pezzoPostOkSede += $pezzoPostOk;
//                    $pezzoPostKoSede += $pezzoPostKo;
//                    $pezzoPostBklSede += $pezzoPostBkl;
//                    $pezzoBollettinoSede += $pezzoBollettino;
//                    $pezzoRidSede += $pezzoRid;
//                    $pezzoCartaceoSede += $pezzoCartaceo;
//                    $pezzoMailSede += $pezzoMail;
//                    $pezzoLuceSede += $pezzoLuce;
//                    $pezzoGasSede += $pezzoGas;
//                    $pezzoDualSede += $pezzoDual;
//                    $pezzoPolizzaSede += $pezzoPolizza;
//
//                    $query = $queryCrm2 . $tipoCampagnaEscluso;
//                    $risultatoCRM2 = $conn19->query($query);
//                    $conteggio = $risultatoCRM2->num_rows;
//                    //echo $conteggio;
//                    if ($conteggio > 0) {
//                        $rigaCRM2 = $risultatoCRM2->fetch_array();
//                        $pezzoLordo = round($rigaCRM2[5], 0);
//                        if ($pesoLordo > 0) {
//                            $pesoLordo = round($rigaCRM2[0], 2);
//                            $pesoPdaOk = round($rigaCRM2[1], 2);
//                            $pesoPdaKo = round($rigaCRM2[2], 2);
//                            $pesoPdaBkl = round($rigaCRM2[3], 2);
//                            $pesoPdaBklp = round($rigaCRM2[4], 2);
//
//                            $pezzoOk = round($rigaCRM2[6], 0);
//                            $pezzoKo = round($rigaCRM2[7], 0);
//                            $pezzoBkl = round($rigaCRM2[8], 0);
//                            $pezzoBklp = round($rigaCRM2[9], 0);
//                            $resaPezzoLordo = 0;
//                            $resaValoreLordo = 0;
//                            $resaPezzoOk = 0;
//                            $resaValoreOk = 0;
//                            $pesoPostOk = round($rigaCRM2[10], 2);
//                            $pesoPostKo = round($rigaCRM2[11], 2);
//                            $pesoPostBkl = round($rigaCRM2[12], 2);
//                            $pezzoPostOk = round($rigaCRM2[13], 0);
//                            $pezzoPostKo = round($rigaCRM2[14], 0);
//                            $pezzoPostBkl = round($rigaCRM2[15], 0);
//                            $pezzoBollettino = round($rigaCRM2[16], 0);
//                            $pezzoRid = round($rigaCRM2[17], 0);
//                            if (($pezzoBollettino + $pezzoRid) == 0) {
//                                $percentualeBollettino = 0;
//                            } else {
//                                $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
//                            }
//                            $pezzoCartaceo = round($rigaCRM2[18], 0);
//                            $pezzoMail = round($rigaCRM2[19], 0);
//                            if (($pezzoCartaceo + $pezzoMail) == 0) {
//                                $percentualeInvio = 0;
//                            } else {
//                                $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
//                            }
//                            $pezzoLuce = round($rigaCRM2[20], 0);
//                            $pezzoGas = round($rigaCRM2[21], 0);
//                            $pezzoDual = round($rigaCRM2[22], 0);
//                            $pezzoPolizza = round($rigaCRM2[23], 0);
//
//                            $html .= "<tr>";
//                            $html .= "<td style='background-color: pink'>$idMandato</td>";
//                            $html .= "<td style='background-color: pink'>$sede</td>";
//
//                            $html .= "<td style='background-color: pink'>ESClUSO</td>";
//
//                            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoLordo</td>";
//                            $html .= "<td>$pesoLordo</td>";
//
//                            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoOk</td>";
//                            $html .= "<td>$pesoPdaOk</td>";
//
//                            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoKo</td>";
//                            $html .= "<td>$pesoPdaKo</td>";
//
//                            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBkl</td>";
//                            $html .= "<td>$pesoPdaBkl</td>";
//
//                            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBklp</td>";
//                            $html .= "<td>$pesoPdaBklp</td>";
//
//                            $html .= "<td style='border-left: 5px double #D0E4F5'>-</td>";
//
//                            $html .= "<td style='border-left: 5px double #D0E4F5'>$resaPezzoLordo</td>";
//                            $html .= "<td>$resaValoreLordo</td>";
//
//                            $html .= "<td style='border-left: 5px double #D0E4F5'>$resaPezzoOk</td>";
//                            $html .= "<td>$resaValoreOk</td>";
//
//                            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBollettino</td>";
//                            $html .= "<td>$pezzoRid</td>";
//                            $html .= "<td>$percentualeBollettino</td>";
//
//                            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoCartaceo</td>";
//                            $html .= "<td>$pezzoMail</td>";
//                            $html .= "<td>$percentualeInvio</td>";
//
//                            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoLuce</td>";
//                            $html .= "<td>$pezzoGas</td>";
//                            $html .= "<td>$pezzoDual</td>";
//                            $html .= "<td>$pezzoPolizza</td>";
//
//                            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostOk</td>";
//                            $html .= "<td>$pesoPostOk</td>";
//
//                            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostKo</td>";
//                            $html .= "<td>$pesoPostKo</td>";
//
//                            $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostBkl</td>";
//                            $html .= "<td>$pesoPostBkl</td>";
//
//                            $html .= "</tr>";
//                        }
//                    }
//                }

                $pesoLordoSede += $pesoLordo;
                $pesoPdaOkSede += $pesoPdaOk;
                $pesoPdaKoSede += $pesoPdaKo;
                $pesoPdaBklSede += $pesoPdaBkl;
                $pesoPdaBklpSede += $pesoPdaBklp;
                $pezzoLordoSede += $pezzoLordo;
                $pezzoOkSede += $pezzoOk;
                $pezzoKoSede += $pezzoKo;
                $pezzoBklSede += $pezzoBkl;
                $pezzoBklpSede += $pezzoBklp;
                $oreSede += $ore;

                $pezzoPostOkSede += $pezzoPostOk;
                $pezzoPostKoSede += $pezzoPostKo;
                $pezzoPostBklSede += $pezzoPostBkl;
                $resaPostOKSede = ($pezzoOkSede == 0) ? 0 : round(($pezzoPostOkSede / $pezzoOkSede) * 100, 2);
                $resaPostKOSede = ($pezzoOkSede == 0) ? 0 : round(($pezzoPostKoSede / $pezzoOkSede) * 100, 2);
                $resaPostBklSede = ($pezzoOkSede == 0) ? 0 : round(($pezzoPostBklSede / $pezzoOkSede) * 100, 2);

                $pezzoBollettinoSede += $pezzoBollettino;
                $pezzoRidSede += $pezzoRid;
                $pezzoCartaceoSede += $pezzoCartaceo;
                $pezzoMailSede += $pezzoMail;
                $pezzoLuceSede += $pezzoLuce;
                $pezzoGasSede += $pezzoGas;
                $pezzoDualSede += $pezzoDual;
                $pezzoPolizzaSede += $pezzoPolizza;

                $resaPezzoLordoSede = ($oreSede == 0) ? 0 : round($pezzoLordoSede / $oreSede, 2);
                $resaValoreLordoSede = ($oreSede == 0) ? 0 : round($pesoLordoSede / $oreSede, 2);
                $resaPezzoOkSede = ($oreSede == 0) ? 0 : round($pezzoOkSede / $oreSede, 2);
                $resaValoreOkSede = ($oreSede == 0) ? 0 : round($pesoPdaOkSede / $oreSede, 2);
                if (($pezzoBollettinoSede + $pezzoRidSede) == 0) {
                    $percentualeBollettinoSede = 0;
                } else {
                    $percentualeBollettinoSede = round((($pezzoBollettinoSede / ($pezzoBollettinoSede + $pezzoRidSede)) * 100), 2);
                }
                if (($pezzoCartaceoSede + $pezzoMailSede) == 0) {
                    $percentualeInvioSede = 0;
                } else {
                    $percentualeInvioSede = ($pezzoCartaceoSede / ($pezzoCartaceoSede + $pezzoMailSede)) * 100;
                }

                $pezzoBollettinoOkSede += $pezzoBollettinoOk;
                $pezzoRidOkSede += $pezzoRidOk;
                $pezzoCartaceoOkSede += $pezzoCartaceoOk;
                $pezzoMailOkSede += $pezzoMailOk;
                if (($pezzoBollettinoOkSede + $pezzoRidOkSede) == 0) {
                    $percentualeBollettinoOkSede = 0;
                } else {
                    $percentualeBollettinoOkSede = round((($pezzoBollettinoOkSede / ($pezzoBollettinoOkSede + $pezzoRidOkSede)) * 100), 2);
                }
                if (($pezzoCartaceoOkSede + $pezzoMailOkSede) == 0) {
                    $percentualeInvioOkSede = 0;
                } else {
                    $percentualeInvioOkSede = ($pezzoMailOkSede / ($pezzoCartaceoOkSede + $pezzoMailOkSede)) * 100;
                }


                $pezzoLordo = 0;
                $pesoLordo = 0;
                $pesoPdaOk = 0;
                $pesoPdaKo = 0;
                $pesoPdaBkl = 0;
                $pesoPdaBklp = 0;
                $pezzoOk = 0;
                $pezzoKo = 0;
                $pezzoBkl = 0;
                $pezzoBklp = 0;
                $resaPezzoLordo = 0;
                $resaValoreLordo = 0;
                $resaPezzoOk = 0;
                $resaValoreOk = 0;
                $pesoPostOk = 0;
                $pesoPostKo = 0;
                $pesoPostBkl = 0;
                $pezzoPostOk = 0;
                $pezzoPostKo = 0;
                $pezzoPostBkl = 0;
                $pezzoBollettino = 0;
                $pezzoRid = 0;
                $percentualeBollettino = 0;
                $pezzoCartaceo = 0;
                $pezzoMail = 0;
                $percentualeInvio = 0;
                $pezzoLuce = 0;
                $pezzoGas = 0;
                $pezzoDual = 0;
                $pezzoPolizza = 0;
                $ore = 0;

                $pezzoBollettinoOk = 0;
                $pezzoRidOk = 0;
                $percentualeBollettinoOk = 0;
                $pezzoCartaceoOk = 0;
                $pezzoMailOk = 0;
                $percentualeInvioOk = 0;

                $html .= "<tr style='background-color: orange'>";
                $html .= "<td colspan='3'>$sede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoLordoSede</td>";
                $html .= "<td>$pesoLordoSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoOkSede</td>";
                $html .= "<td>$pesoPdaOkSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoKoSede</td>";
                $html .= "<td>$pesoPdaKoSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBklSede</td>";
                $html .= "<td>$pesoPdaBklSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBklpSede</td>";
                $html .= "<td>$pesoPdaBklpSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$oreSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$resaPezzoLordoSede</td>";
                $html .= "<td>$resaValoreLordoSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$resaPezzoOkSede</td>";
                $html .= "<td>$resaValoreOkSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBollettinoSede</td>";
                $html .= "<td>$pezzoRidSede</td>";
                $html .= "<td>$percentualeBollettinoSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBollettinoOkSede</td>";
                $html .= "<td>$pezzoRidOkSede</td>";
                $html .= "<td>$percentualeBollettinoOkSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoCartaceoSede</td>";
                $html .= "<td>$pezzoMailSede</td>";
                $html .= "<td>$percentualeInvioSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoCartaceoOkSede</td>";
                $html .= "<td>$pezzoMailOkSede</td>";
                $html .= "<td>$percentualeInvioOkSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoLuceSede</td>";
                $html .= "<td>$pezzoGasSede</td>";
                $html .= "<td>$pezzoDualSede</td>";
                $html .= "<td>$pezzoPolizzaSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostOkSede</td>";
                $html .= "<td>$pesoPostOkSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostKoSede</td>";
                $html .= "<td>$pesoPostKoSede</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostBklSede</td>";
                $html .= "<td>$pesoPostBklSede</td>";

                $html .= "</tr>";
                $tipoCampagnaEscluso = "";
                $pesoLordoTotalePolizze += $pesoLordoSede;
                $pesoPdaOkTotalePolizze += $pesoPdaOkSede;
                $pesoPdaKoTotalePolizze += $pesoPdaKoSede;
                $pesoPdaBklTotalePolizze += $pesoPdaBklSede;
                $pesoPdaBklpTotalePolizze += $pesoPdaBklpSede;
                $pezzoLordoTotalePolizze += $pezzoLordoSede;
                $pezzoOkTotalePolizze += $pezzoOkSede;
                $pezzoKoTotalePolizze += $pezzoKoSede;
                $pezzoBklTotalePolizze += $pezzoBklSede;
                $pezzoBklpTotalePolizze += $pezzoBklpSede;
                $oreTotalePolizze += $oreSede;

                $pezzoPostOkTotalePolizze += $pezzoPostOkSede;
                $pezzoPostKoTotalePolizze += $pezzoPostKoSede;
                $pezzoPostBklTotalePolizze += $pezzoPostBklSede;
                $pezzoBollettinoTotalePolizze += $pezzoBollettinoSede;
                $pezzoRidTotalePolizze += $pezzoRidSede;
                $pezzoCartaceoTotalePolizze += $pezzoCartaceoSede;
                $pezzoMailTotalePolizze += $pezzoMailSede;
                $pezzoLuceTotalePolizze += $pezzoLuceSede;
                $pezzoGasTotalePolizze += $pezzoGasSede;
                $pezzoDualTotalePolizze += $pezzoDualSede;

                $pezzoBollettinoOkTotalePolizze += $pezzoBollettinoOkSede;
                $pezzoRidOkTotalePolizze += $pezzoRidOkSede;
                $pezzoCartaceoOkTotalePolizze += $pezzoCartaceoOkSede;
                $pezzoMailOkTotalePolizze += $pezzoMailOkSede;

//                $pezzoPolizzaTotale+=$pezzoPolizzaSede;
                $pesoLordoSede = 0;
                $pesoPdaOkSede = 0;
                $pesoPdaKoSede = 0;
                $pesoPdaBklSede = 0;
                $pesoPdaBklpSede = 0;
                $pezzoLordoSede = 0;
                $pezzoOkSede = 0;
                $pezzoKoSede = 0;
                $pezzoBklSede = 0;
                $pezzoBklpSede = 0;
                $oreSede = 0;
                $pesoPostOkSede = 0;
                $pesoPostKoSede = 0;
                $pesoPostBklSede = 0;
                $pezzoPostOkSede = 0;
                $pezzoPostKoSede = 0;
                $pezzoPostBklSede = 0;
                $pezzoBollettinoSede = 0;
                $pezzoRidSede = 0;
                $pezzoCartaceoSede = 0;
                $pezzoMailSede = 0;
                $pezzoLuceSede = 0;
                $pezzoGasSede = 0;
                $pezzoDualSede = 0;

                $pezzoBollettinoOkSede = 0;
                $pezzoRidOkSede = 0;
                $pezzoCartaceoOkSede = 0;
                $pezzoMailOkSede = 0;
            }
        } else {
            $pesoLordoSede += $pesoLordo;
            $pesoPdaOkSede += $pesoPdaOk;
            $pesoPdaKoSede += $pesoPdaKo;
            $pesoPdaBklSede += $pesoPdaBkl;
            $pesoPdaBklpSede += $pesoPdaBklp;
            $pezzoLordoSede += $pezzoLordo;
            $pezzoOkSede += $pezzoOk;
            $pezzoKoSede += $pezzoKo;
            $pezzoBklSede += $pezzoBkl;
            $pezzoBklpSede += $pezzoBklp;
            $oreSede += $ore;

            $pezzoPostOkSede += $pezzoPostOk;
            $pezzoPostKoSede += $pezzoPostKo;
            $pezzoPostBklSede += $pezzoPostBkl;
            $pezzoBollettinoSede += $pezzoBollettino;
            $pezzoRidSede += $pezzoRid;
            $pezzoCartaceoSede += $pezzoCartaceo;
            $pezzoMailSede += $pezzoMail;
            $pezzoLuceSede += $pezzoLuce;
            $pezzoGasSede += $pezzoGas;
            $pezzoDualSede += $pezzoDual;
            $pezzoPolizzaSede += $pezzoPolizza;

            $pezzoBollettinoOkSede += $pezzoBollettinoOk;
            $pezzoRidOkSede += $pezzoRidOk;
            $pezzoCartaceoOkSede += $pezzoCartaceoOk;
            $pezzoMailOkSede += $pezzoMailOk;

            $pezzoLordo = 0;
            $pesoLordo = 0;
            $pesoPdaOk = 0;
            $pesoPdaKo = 0;
            $pesoPdaBkl = 0;
            $pesoPdaBklp = 0;
            $pezzoOk = 0;
            $pezzoKo = 0;
            $pezzoBkl = 0;
            $pezzoBklp = 0;
            $resaPezzoLordo = 0;
            $resaValoreLordo = 0;
            $resaPezzoOk = 0;
            $resaValoreOk = 0;
            $pesoPostOk = 0;
            $pesoPostKo = 0;
            $pesoPostBkl = 0;
            $pezzoPostOk = 0;
            $pezzoPostKo = 0;
            $pezzoPostBkl = 0;
            $pezzoBollettino = 0;
            $pezzoRid = 0;
            $percentualeBollettino = 0;
            $pezzoCartaceo = 0;
            $pezzoMail = 0;
            $percentualeInvio = 0;
            $pezzoLuce = 0;
            $pezzoGas = 0;
            $pezzoDual = 0;
            $pezzoPolizza = 0;
            $ore = 0;

            $pezzoBollettinoOk = 0;
            $pezzoRidOk = 0;
            $percentualeBollettinoOk = 0;
            $pezzoCartaceoOk = 0;
            $pezzoMailOk = 0;
            $percentualeInvioOk = 0;
        }
    }
}
$html .= "</tr></table>";
//echo $html;


    