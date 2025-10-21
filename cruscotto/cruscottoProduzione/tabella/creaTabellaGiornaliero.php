<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$testMode = $_POST["testMode"];

//$mandato = json_decode($_POST["mandato"], true);
$mandato = "Plenitude";
$dataMinoreIta = date("d-m-Y");
$dataMaggioreIta = date("d-m-Y");
$data = date("Y-m-d");

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

$pesoLordoTotale = 0;
$pesoPdaOkTotale = 0;
$pesoPdaKoTotale = 0;
$pesoPdaBklTotale = 0;
$pesoPdaBklpTotale = 0;
$pezzoLordoTotale = 0;
$pezzoOkTotale = 0;
$pezzoKoTotale = 0;
$pezzoBklTotale = 0;
$pezzoBklpTotale = 0;
$oreTotale = 0;
$pesoPostOkTotale = 0;
$pesoPostKoTotale = 0;
$pesoPostBklTotale = 0;
$pezzoPostOkTotale = 0;
$pezzoPostKoTotale = 0;
$pezzoPostBklTotale = 0;
$pezzoBollettinoTotale = 0;
$pezzoRidTotale = 0;
$pezzoCartaceoTotale = 0;
$pezzoMailTotale = 0;
$pezzoLuceTotale = 0;
$pezzoGasTotale = 0;
$pezzoDualTotale = 0;
$pezzoPolizzaTotale = 0;

$html = "<table class='blueTable'>";
include "../../tabella/intestazioneTabella.php";


        $queryCampagna = "SELECT tipo FROM `plenitudeCampagna` where tipo is not null group by tipo";

        $querySedi = "SELECT sede FROM `plenitude` where data='$data' group by sede";

        $queryControllo = "SELECT plenitude.id "
                . " FROM "
                . " plenitude "
                . " inner JOIN aggiuntaPlenitude on plenitude.id=aggiuntaPlenitude.id "
                . " where "
                . " data='$data'  "
                . " and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco' and comodity<>'Polizza'";

        $queryCrm = "SELECT "
                . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                . " sum(if(fasePost='OK',if(fasePDA='OK',pezzoLordo,0),0)),sum(if(fasePost='KO',if(fasePDA='OK',pezzoLordo,0),0)), sum(if(fasePost='BKL',if(fasePDA='OK',pezzoLordo,0),0)), "
                . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0))"
                . "FROM "
                . "plenitude "
                . "inner JOIN aggiuntaPlenitude on plenitude.id=aggiuntaPlenitude.id "
                . "where data='$data'  "
                . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco' and comodity<>'Polizza'";
       
//    case "Vivigas Energia":
//        $queryCampagna = "SELECT tipo FROM vivigasCampagna where tipo is not null group by tipo";
//        
//        $querySedi = "SELECT sede FROM `vivigas` where data='$data' group by sede";
//        
//        $queryControllo="SELECT "
//                . "vivigas.id "
//                . "FROM "
//                . "vivigas "
//                . "inner JOIN aggiuntaVivigas on vivigas.id=aggiuntaVivigas.id "
//                . "where "
//                . "data='$data'  "
//                . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia'";
//        
//        
//        $queryCrm = "SELECT "
//                . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
//                . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
//                . " sum(if(fasePost='OK',if(fasePDA='OK',pezzoLordo,0),0)),sum(if(fasePost='KO',if(fasePDA='OK',pezzoLordo,0),0)), sum(if(fasePost='BKL',if(fasePDA='OK',pezzoLordo,0),0)), "
//                . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
//                . " sum(if(metodoPagamento='Bollettino',pezzoLordo,0)),sum(if(metodoPagamento='SSD',pezzoLordo,0)),"
//                . " sum(if(metodoInvio='Posta (Residenza)',pezzoLordo,0)),sum(if(metodoInvio='Mail',pezzoLordo,0)), "
//                . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0))"
//                . "FROM "
//                . "vivigas "
//                . "inner JOIN aggiuntaVivigas on vivigas.id=aggiuntaVivigas.id "
//                . "where "
//                . "data='$data'  "
//                . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia'";
//
//        break;
    

$risultatoSede = $conn19->query($querySedi);
while ($rigaSedi = $risultatoSede->fetch_array()) {
    $sede = $rigaSedi[0];
    $andSede = " and sede='$sede' ";
//    echo $andSede . "<br>";

    $risultatoCampagna = $conn19->query($queryCampagna);
    while ($rigaCampagna = $risultatoCampagna->fetch_array()) {
        $campagna = $rigaCampagna[0];
        $andCampagna = " and tipoCampagna='$campagna' ";
//        echo $andCampagna . " ";
        $risultatoControllo = $conn19->query($queryControllo . $andSede . $andCampagna);
        if (($risultatoControllo->num_rows) > 0) {
            $risultatoCrm = $conn19->query($queryCrm . $andSede . $andCampagna);
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
                $resaPezzoLordo = "";
                $resaValoreLordo = "";
                $resaPezzoOk = "";
                $resaValoreOk = "";
                $pesoPostOk = 0;
                $pesoPostKo = 0;
                $pesoPostBkl = 0;
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
                    $percentualeBollettino = round((($pezzoRid / ($pezzoBollettino + $pezzoRid)) * 100), 2);
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

                $html .= "<tr>";
                $html .= "<td style='background-color: pink'>$mandato</td>";
                $html .= "<td style='background-color: pink'>$sede</td>";
                $html .= "<td style='background-color: pink'>$campagna</td>";

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

                $html .= "<td style='border-left: 5px double #D0E4F5'></td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$resaPezzoLordo</td>";
                $html .= "<td>$resaValoreLordo</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$resaPezzoOk</td>";
                $html .= "<td>$resaValoreOk</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBollettino</td>";
                $html .= "<td>$pezzoRid</td>";
                $html .= "<td>$percentualeBollettino</td>";

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
                $oreSede += 0;

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

                $resaPezzoLordoSede = "";
                $resaValoreLordoSede = "";
                $resaPezzoOkSede = "";
                $resaValoreOkSede = "";
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
            }
        }
    }
    if ($pezzoLordoSede<>0){
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

    $html .= "<td style='border-left: 5px double #D0E4F5'></td>";

    $html .= "<td style='border-left: 5px double #D0E4F5'></td>";
    $html .= "<td></td>";

    $html .= "<td style='border-left: 5px double #D0E4F5'></td>";
    $html .= "<td></td>";

    $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBollettinoSede</td>";
    $html .= "<td>$pezzoRidSede</td>";
    $html .= "<td></td>";

    $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoCartaceoSede</td>";
    $html .= "<td>$pezzoMailSede</td>";
    $html .= "<td></td>";

    $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoLuceSede</td>";
    $html .= "<td>$pezzoGasSede</td>";
    $html .= "<td>$pezzoDualSede</td>";
    $html .= "<td>$pezzoPolizzaSede</td>";

    $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostOkSede</td>";
    $html .= "<td></td>";

    $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostKoSede</td>";
    $html .= "<td></td>";

    $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostBklSede</td>";
    $html .= "<td></td>";

    $html .= "</tr>";
    }
    $pesoLordoTotale += $pesoLordoSede;
    $pesoPdaOkTotale += $pesoPdaOkSede;
    $pesoPdaKoTotale += $pesoPdaKoSede;
    $pesoPdaBklTotale += $pesoPdaBklSede;
    $pesoPdaBklpTotale += $pesoPdaBklpSede;
    $pezzoLordoTotale += $pezzoLordoSede;
    $pezzoOkTotale += $pezzoOkSede;
    $pezzoKoTotale += $pezzoKoSede;
    $pezzoBklTotale += $pezzoBklSede;
    $pezzoBklpTotale += $pezzoBklpSede;
    $oreTotale += round($oreSede, 2);

    $pezzoPostOkTotale += $pezzoPostOkSede;
    $pezzoPostKoTotale += $pezzoPostKoSede;
    $pezzoPostBklTotale += $pezzoPostBklSede;

    $resaPostOKTotale = ($pezzoOkTotale == 0) ? 0 : round(($pezzoPostOkTotale / $pezzoOkTotale) * 100, 2);
    $resaPostKOTotale = ($pezzoOkTotale == 0) ? 0 : round(($pezzoPostKoTotale / $pezzoOkTotale) * 100, 2);
    $resaPostBklTotale = ($pezzoOkTotale == 0) ? 0 : round(($pezzoPostBklTotale / $pezzoOkTotale) * 100, 2);

    $pezzoBollettinoTotale += $pezzoBollettinoSede;
    $pezzoRidTotale += $pezzoRidSede;
    $pezzoCartaceoTotale += $pezzoCartaceoSede;
    $pezzoMailTotale += $pezzoMailSede;
    $pezzoLuceTotale += $pezzoLuceSede;
    $pezzoGasTotale += $pezzoGasSede;
    $pezzoDualTotale += $pezzoDualSede;
    $pezzoPolizzaTotale += $pezzoPolizzaSede;

    $resaPezzoLordoTotale = "";
    $resaValoreLordoTotale = "";
    $resaPezzoOkTotale = "";
    $resaValoreOkTotale = "";

    if (($pezzoBollettinoTotale + $pezzoRidTotale) == 0) {
        $percentualeBollettinoTotale = 0;
    } else {
        $percentualeBollettinoTotale = round((($pezzoBollettinoTotale / ($pezzoBollettinoTotale + $pezzoRidTotale)) * 100), 2);
    }
    if (($pezzoCartaceoTotale + $pezzoMailTotale) == 0) {
        $percentualeInvioTotale = 0;
    } else {
        $percentualeInvioTotale = ($pezzoCartaceoTotale / ($pezzoCartaceoTotale + $pezzoMailTotale)) * 100;
    }
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
}
$html .= "<tr style='background-color: orangered'>";
$html .= "<td colspan='3'>TOTALE</td>";

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoLordoTotale</td>";
$html .= "<td>$pesoLordoTotale</td>";

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoOkTotale</td>";
$html .= "<td>$pesoPdaOkTotale</td>";

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoKoTotale</td>";
$html .= "<td>$pesoPdaKoTotale</td>";

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBklTotale</td>";
$html .= "<td>$pesoPdaBklTotale</td>";

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBklpTotale</td>";
$html .= "<td>$pesoPdaBklpTotale</td>";

$html .= "<td style='border-left: 5px double #D0E4F5'></td>";

$html .= "<td style='border-left: 5px double #D0E4F5'></td>";
$html .= "<td></td>";

$html .= "<td style='border-left: 5px double #D0E4F5'></td>";
$html .= "<td></td>";

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBollettinoTotale</td>";
$html .= "<td>$pezzoRidTotale</td>";
$html .= "<td></td>";

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoCartaceoTotale</td>";
$html .= "<td>$pezzoMailTotale</td>";
$html .= "<td></td>";

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoLuceTotale</td>";
$html .= "<td>$pezzoGasTotale</td>";
$html .= "<td>$pezzoDualTotale</td>";
$html .= "<td>$pezzoPolizzaTotale</td>";

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostOkTotale</td>";
$html .= "<td></td>";

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostKoTotale</td>";
$html .= "<td></td>";

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostBklTotale</td>";
$html .= "<td></td>";

$pesoLordoTotale =0;
    $pesoPdaOkTotale =0;
    $pesoPdaKoTotale =0;
    $pesoPdaBklTotale =0;
    $pesoPdaBklpTotale =0;
    $pezzoLordoTotale =0;
    $pezzoOkTotale =0;
    $pezzoKoTotale =0;
    $pezzoBklTotale =0;
    $pezzoBklpTotale =0;
    $oreTotale =0;

    $pezzoPostOkTotale =0;
    $pezzoPostKoTotale =0;
    $pezzoPostBklTotale =0;

    

    $pezzoBollettinoTotale =0;
    $pezzoRidTotale =0;
    $pezzoCartaceoTotale =0;
    $pezzoMailTotale =0;
    $pezzoLuceTotale =0;
    $pezzoGasTotale =0;
    $pezzoDualTotale =0;
    $pezzoPolizzaTotale =0;

    $resaPezzoLordoTotale = "";
    $resaValoreLordoTotale = "";
    $resaPezzoOkTotale = "";
    $resaValoreOkTotale = "";

//$html .= "</tr>";
//$html .= "</table>";
//$html .= "<br>";



$mandato2="Vivigas";

        $queryCampagna = "SELECT tipo FROM vivigasCampagna where tipo is not null group by tipo";
        
        $querySedi = "SELECT sede FROM `vivigas` where data='$data' group by sede";
        
        $queryControllo="SELECT "
                . "vivigas.id "
                . "FROM "
                . "vivigas "
                . "inner JOIN aggiuntaVivigas on vivigas.id=aggiuntaVivigas.id "
                . "where "
                . "data='$data'  "
                . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia'";
        
        
        $queryCrm = "SELECT "
                . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                . " sum(if(fasePost='OK',if(fasePDA='OK',pezzoLordo,0),0)),sum(if(fasePost='KO',if(fasePDA='OK',pezzoLordo,0),0)), sum(if(fasePost='BKL',if(fasePDA='OK',pezzoLordo,0),0)), "
                . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                . " sum(if(metodoPagamento='Bollettino',pezzoLordo,0)),sum(if(metodoPagamento='SSD',pezzoLordo,0)),"
                . " sum(if(metodoInvio='Posta (Residenza)',pezzoLordo,0)),sum(if(metodoInvio='Mail',pezzoLordo,0)), "
                . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0))"
                . "FROM "
                . "vivigas "
                . "inner JOIN aggiuntaVivigas on vivigas.id=aggiuntaVivigas.id "
                . "where "
                . "data='$data'  "
                . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia'";

    

$risultatoSede = $conn19->query($querySedi);
while ($rigaSedi = $risultatoSede->fetch_array()) {
    $sede = $rigaSedi[0];
    $andSede = " and sede='$sede' ";
//    echo $andSede . "<br>";

    $risultatoCampagna = $conn19->query($queryCampagna);
    while ($rigaCampagna = $risultatoCampagna->fetch_array()) {
        $campagna = $rigaCampagna[0];
        $andCampagna = " and tipoCampagna='$campagna' ";
//        echo $andCampagna . " ";
        $risultatoControllo = $conn19->query($queryControllo . $andSede . $andCampagna);
        if (($risultatoControllo->num_rows) > 0) {
            $risultatoCrm = $conn19->query($queryCrm . $andSede . $andCampagna);
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
                $resaPezzoLordo = "";
                $resaValoreLordo = "";
                $resaPezzoOk = "";
                $resaValoreOk = "";
                $pesoPostOk = 0;
                $pesoPostKo = 0;
                $pesoPostBkl = 0;
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
                    $percentualeBollettino = round((($pezzoRid / ($pezzoBollettino + $pezzoRid)) * 100), 2);
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

                $html .= "<tr>";
                $html .= "<td style='background-color: pink'>$mandato2</td>";
                $html .= "<td style='background-color: pink'>$sede</td>";
                $html .= "<td style='background-color: pink'>$campagna</td>";

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

                $html .= "<td style='border-left: 5px double #D0E4F5'></td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$resaPezzoLordo</td>";
                $html .= "<td>$resaValoreLordo</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$resaPezzoOk</td>";
                $html .= "<td>$resaValoreOk</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBollettino</td>";
                $html .= "<td>$pezzoRid</td>";
                $html .= "<td>$percentualeBollettino</td>";

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
                $oreSede += 0;

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

                $resaPezzoLordoSede = "";
                $resaValoreLordoSede = "";
                $resaPezzoOkSede = "";
                $resaValoreOkSede = "";
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
            }
        }
    }
    if ($pezzoLordoSede<>0){
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

    $html .= "<td style='border-left: 5px double #D0E4F5'></td>";

    $html .= "<td style='border-left: 5px double #D0E4F5'></td>";
    $html .= "<td></td>";

    $html .= "<td style='border-left: 5px double #D0E4F5'></td>";
    $html .= "<td></td>";

    $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBollettinoSede</td>";
    $html .= "<td>$pezzoRidSede</td>";
    $html .= "<td></td>";

    $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoCartaceoSede</td>";
    $html .= "<td>$pezzoMailSede</td>";
    $html .= "<td></td>";

    $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoLuceSede</td>";
    $html .= "<td>$pezzoGasSede</td>";
    $html .= "<td>$pezzoDualSede</td>";
    $html .= "<td>$pezzoPolizzaSede</td>";

    $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostOkSede</td>";
    $html .= "<td></td>";

    $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostKoSede</td>";
    $html .= "<td></td>";

    $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostBklSede</td>";
    $html .= "<td></td>";

    $html .= "</tr>";
    }
    $pesoLordoTotale += $pesoLordoSede;
    $pesoPdaOkTotale += $pesoPdaOkSede;
    $pesoPdaKoTotale += $pesoPdaKoSede;
    $pesoPdaBklTotale += $pesoPdaBklSede;
    $pesoPdaBklpTotale += $pesoPdaBklpSede;
    $pezzoLordoTotale += $pezzoLordoSede;
    $pezzoOkTotale += $pezzoOkSede;
    $pezzoKoTotale += $pezzoKoSede;
    $pezzoBklTotale += $pezzoBklSede;
    $pezzoBklpTotale += $pezzoBklpSede;
    $oreTotale += round($oreSede, 2);

    $pezzoPostOkTotale += $pezzoPostOkSede;
    $pezzoPostKoTotale += $pezzoPostKoSede;
    $pezzoPostBklTotale += $pezzoPostBklSede;

    $resaPostOKTotale = ($pezzoOkTotale == 0) ? 0 : round(($pezzoPostOkTotale / $pezzoOkTotale) * 100, 2);
    $resaPostKOTotale = ($pezzoOkTotale == 0) ? 0 : round(($pezzoPostKoTotale / $pezzoOkTotale) * 100, 2);
    $resaPostBklTotale = ($pezzoOkTotale == 0) ? 0 : round(($pezzoPostBklTotale / $pezzoOkTotale) * 100, 2);

    $pezzoBollettinoTotale += $pezzoBollettinoSede;
    $pezzoRidTotale += $pezzoRidSede;
    $pezzoCartaceoTotale += $pezzoCartaceoSede;
    $pezzoMailTotale += $pezzoMailSede;
    $pezzoLuceTotale += $pezzoLuceSede;
    $pezzoGasTotale += $pezzoGasSede;
    $pezzoDualTotale += $pezzoDualSede;
    $pezzoPolizzaTotale += $pezzoPolizzaSede;

    $resaPezzoLordoTotale = "";
    $resaValoreLordoTotale = "";
    $resaPezzoOkTotale = "";
    $resaValoreOkTotale = "";

    if (($pezzoBollettinoTotale + $pezzoRidTotale) == 0) {
        $percentualeBollettinoTotale = 0;
    } else {
        $percentualeBollettinoTotale = round((($pezzoBollettinoTotale / ($pezzoBollettinoTotale + $pezzoRidTotale)) * 100), 2);
    }
    if (($pezzoCartaceoTotale + $pezzoMailTotale) == 0) {
        $percentualeInvioTotale = 0;
    } else {
        $percentualeInvioTotale = ($pezzoCartaceoTotale / ($pezzoCartaceoTotale + $pezzoMailTotale)) * 100;
    }
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
}
$html .= "<tr style='background-color: orangered'>";
$html .= "<td colspan='3'>TOTALE</td>";

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoLordoTotale</td>";
$html .= "<td>$pesoLordoTotale</td>";

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoOkTotale</td>";
$html .= "<td>$pesoPdaOkTotale</td>";

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoKoTotale</td>";
$html .= "<td>$pesoPdaKoTotale</td>";

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBklTotale</td>";
$html .= "<td>$pesoPdaBklTotale</td>";

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBklpTotale</td>";
$html .= "<td>$pesoPdaBklpTotale</td>";

$html .= "<td style='border-left: 5px double #D0E4F5'></td>";

$html .= "<td style='border-left: 5px double #D0E4F5'></td>";
$html .= "<td></td>";

$html .= "<td style='border-left: 5px double #D0E4F5'></td>";
$html .= "<td></td>";

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBollettinoTotale</td>";
$html .= "<td>$pezzoRidTotale</td>";
$html .= "<td></td>";

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoCartaceoTotale</td>";
$html .= "<td>$pezzoMailTotale</td>";
$html .= "<td></td>";

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoLuceTotale</td>";
$html .= "<td>$pezzoGasTotale</td>";
$html .= "<td>$pezzoDualTotale</td>";
$html .= "<td>$pezzoPolizzaTotale</td>";

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostOkTotale</td>";
$html .= "<td></td>";

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostKoTotale</td>";
$html .= "<td></td>";

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostBklTotale</td>";
$html .= "<td></td>";

$html .= "</tr>";
$html .= "</table>";
$html .= "<br>";


    
    include "creaTabellaGiornalieroPolizza.php";


echo $html;

