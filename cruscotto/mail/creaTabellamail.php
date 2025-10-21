<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$dataMinore = filter_input(INPUT_POST, "dataMinore");
//echo $dataMinore;
$dataMaggiore = filter_input(INPUT_POST, "dataMaggiore");
$testMode = $_POST["testMode"];
//echo $testMode;
//if ($testMode=="true") {
//    echo "si";
//}
$mandato = json_decode($_POST["mandato"], true);
$sede = json_decode($_POST["sede"], true);

$dataMinoreIta = date('d-m-Y', strtotime($dataMinore));
$dataMaggioreIta = date('d-m-Y', strtotime($dataMaggiore));

$queryMandato = "";
$lunghezza = count($mandato);

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

if ($lunghezza == 1) {
    $queryMandato .= " AND idMandato='$mandato[0]' ";
} else {
    for ($i = 0; $i < $lunghezza; $i++) {
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
    for ($l = 0; $l < $lunghezzaSede; $l++) {
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
$queryGroupMandato = "SELECT sede,idMandato,sum(numero)/3600 as ore, mandato.tipoCampagna,lead(sede) over (order by sede,idMandato,tipoCampagna) "
        . "FROM `stringheTotale` inner join mandato on stringheTotale.mandato=mandato.descrizione "
        . "where giorno>='$dataMinore' and giorno<='$dataMaggiore' and livello=1  "
        . "" . $queryMandato . $querySede . "group by sede,idMandato,mandato.tipoCampagna order by sede,idMandato,tipoCampagna";
//echo $queryGroupMandato;
$risultatoQueryGroupMandato = $conn19->query($queryGroupMandato);
$html = "<table class='blueTable'>";
include "tabella/intestazioneTabella.php";

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
                    . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0))"
                    . "FROM "
                    . "plenitude "
                    . "inner JOIN aggiuntaPlenitude on plenitude.id=aggiuntaPlenitude.id "
                    . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede' and tipoCampagna='$descrizioneMandato' "
                    . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco' and comodity<>'Polizza'";

            $queryCrm2 = "SELECT "
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
                    . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede'  "
                    . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco'";

            break;
        case "Green Network":
            $queryCrm = "SELECT "
                    . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                    . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                    . " sum(if(fasePost='OK',if(fasePDA='OK',pezzoLordo,0),0)),sum(if(fasePost='KO',if(fasePDA='OK',pezzoLordo,0),0)), sum(if(fasePost='BKL',if(fasePDA='OK',pezzoLordo,0),0)), "
                    . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                    . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                    . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                    . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0))"
                    . "FROM "
                    . "green "
                    . "inner JOIN aggiuntaGreen on green.id=aggiuntaGreen.id "
                    . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede' and tipoCampagna='$descrizioneMandato'"
                    . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia'";

            $queryCrm2 = "SELECT "
                    . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                    . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                    . " sum(if(fasePost='OK',if(fasePDA='OK',pezzoLordo,0),0)),sum(if(fasePost='KO',if(fasePDA='OK',pezzoLordo,0),0)), sum(if(fasePost='BKL',if(fasePDA='OK',pezzoLordo,0),0)), "
                    . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                    . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                    . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                    . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0))"
                    . "FROM "
                    . "green "
                    . "inner JOIN aggiuntaGreen on green.id=aggiuntaGreen.id "
                    . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede'  "
                    . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia'";
            break;
        case "Vivigas Energia":
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
                    . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede' and tipoCampagna='$descrizioneMandato'"
                    . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia'";

            $queryCrm2 = "SELECT "
                    . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                    . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                    . " sum(if(fasePost='OK',if(fasePDA='OK',pezzoLordo,0),0)),sum(if(fasePost='KO',if(fasePDA='OK',pezzoLordo,0),0)), sum(if(fasePost='BKL',if(fasePDA='OK',pezzoLordo,0),0)), "
                    . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                    . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                    . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                    . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0))"
                    . "FROM "
                    . "vivigas "
                    . "inner JOIN aggiuntaVivigas on vivigas.id=aggiuntaVivigas.id "
                    . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede'  "
                    . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia'";
            break;
        case "Vodafone":
            $queryCrm = "SELECT "
                    . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                    . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                    . " sum(if(fasePost='OK',if(fasePDA='OK',pezzoLordo,0),0)),sum(if(fasePost='KO',if(fasePDA='OK',pezzoLordo,0),0)), sum(if(fasePost='BKL',if(fasePDA='OK',pezzoLordo,0),0)), "
                    . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                    . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                    . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                    . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0))"
                    . "FROM "
                    . "vodafone "
                    . "inner JOIN aggiuntaVodafone on vodafone.id=aggiuntaVodafone.id "
                    . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede' and tipoCampagna='$descrizioneMandato'"
                    . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia'";

            $queryCrm2 = "SELECT "
                    . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                    . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                    . " sum(if(fasePost='OK',if(fasePDA='OK',pezzoLordo,0),0)),sum(if(fasePost='KO',if(fasePDA='OK',pezzoLordo,0),0)), sum(if(fasePost='BKL',if(fasePDA='OK',pezzoLordo,0),0)), "
                    . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                    . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                    . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                    . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0))"
                    . "FROM "
                    . "vodafone "
                    . "inner JOIN aggiuntaVodafone on vodafone.id=aggiuntaVodafone.id "
                    . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede'  "
                    . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia'";
            break;
        case "enel_out":
            $queryCrm = "SELECT "
                    . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                    . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                    . " sum(if(fasePost='OK',if(fasePDA='OK',pezzoLordo,0),0)),sum(if(fasePost='KO',if(fasePDA='OK',pezzoLordo,0),0)), sum(if(fasePost='BKL',if(fasePDA='OK',pezzoLordo,0),0)), "
                    . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                    . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                    . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                    . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0))"
                    . "FROM "
                    . "enelOut "
                    . "inner JOIN aggiuntaEnelOut on enelOut.id=aggiuntaEnelOut.id "
                    . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede' and tipoCampagna='$descrizioneMandato'"
                    . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia'";
            //echo $queryCrm;

            $queryCrm2 = "SELECT "
                    . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                    . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                    . " sum(if(fasePost='OK',if(fasePDA='OK',pezzoLordo,0),0)),sum(if(fasePost='KO',if(fasePDA='OK',pezzoLordo,0),0)), sum(if(fasePost='BKL',if(fasePDA='OK',pezzoLordo,0),0)), "
                    . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                    . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                    . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                    . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0))"
                    . "FROM "
                    . "enelOut "
                    . "inner JOIN aggiuntaEnelOut on enelOut.id=aggiuntaEnelOut.id "
                    . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede'  "
                    . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia'";

            break;
    }
    //echo "<br>";
    //echo $queryCrm;
    //echo "<br>";
    $tipoCampagnaEscluso .= " and tipoCampagna<>'$descrizioneMandato'  ";

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

        $html .= "<td style='border-left: 5px double #D0E4F5'>" . round($ore, 2) . "</td>";

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
                    $pesoPostOkSede += $pesoPostOk;
                    $pesoPostKoSede += $pesoPostKo;
                    $pesoPostBklSede += $pesoPostBkl;
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

                    $query = $queryCrm2 . $tipoCampagnaEscluso;
                    $risultatoCRM2 = $conn19->query($query);
                    $conteggio = $risultatoCRM2->num_rows;
                    //echo $query;
                    if ($conteggio > 0) {

                        $rigaCRM2 = $risultatoCRM2->fetch_array();
                        $pesoLordo = round($rigaCRM2[0], 2);
                        $pesoPdaOk = round($rigaCRM2[1], 2);
                        $pesoPdaKo = round($rigaCRM2[2], 2);
                        $pesoPdaBkl = round($rigaCRM2[3], 2);
                        $pesoPdaBklp = round($rigaCRM2[4], 2);
                        $pezzoLordo = round($rigaCRM2[5], 0);
                        $pezzoOk = round($rigaCRM2[6], 0);
                        $pezzoKo = round($rigaCRM2[7], 0);
                        $pezzoBkl = round($rigaCRM2[8], 0);
                        $pezzoBklp = round($rigaCRM2[9], 0);
                        $resaPezzoLordo = 0;
                        $resaValoreLordo = 0;
                        $resaPezzoOk = 0;
                        $resaValoreOk = 0;
                        $pesoPostOk = 0;
                        $pesoPostKo = 0;
                        $pesoPostBkl = 0;
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
                            $percentualeBollettino = round((($pezzoRid / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                        }
                        $pezzoCartaceo = round($rigaCRM2[18], 0);
                        $pezzoMail = round($rigaCRM2[19], 0);
                        if (($pezzoCartaceo + $pezzoMail) == 0) {
                            $percentualeInvio = 0;
                        } else {
                            $percentualeInvio = ($pezzoCartaceo / ($pezzoMail + $pezzoMail)) * 100;
                        }
                        $pezzoLuce = round($rigaCRM2[20], 0);
                        $pezzoGas = round($rigaCRM2[21], 0);
                        $pezzoDual = round($rigaCRM2[22], 0);
                        $pezzoPolizza = round($rigaCRM2[23], 0);

                        $html .= "<tr>";
                        $html .= "<td style='background-color: pink'>$idMandato</td>";
                        $html .= "<td style='background-color: pink'>$sede</td>";

                        $html .= "<td style='background-color: pink'>ESClUSO</td>";

                        $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoLordo</td>";
                        $html .= "<td>$pesoLordo</td>";

                        $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoOk</td>";
                        $html .= "<td>$pesoPdaOk</td>";

                        $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoKo</td>";
                        $html .= "<td>$resaPostOK</td>";

                        $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBkl</td>";
                        $html .= "<td>$resaPostKO</td>";

                        $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBklp</td>";
                        $html .= "<td>$resaPostBkl</td>";

                        $html .= "<td style='border-left: 5px double #D0E4F5'>-</td>";

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
                        $html .= "<td>$pesoPostOk</td>";

                        $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostKo</td>";
                        $html .= "<td>$pesoPostKo</td>";

                        $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostBkl</td>";
                        $html .= "<td>$pesoPostBkl</td>";

                        $html .= "</tr>";
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
                $oreSede += round($ore,2);

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

                $resaPezzoLordoSede = round($pezzoLordoSede / $oreSede, 2);
                $resaValoreLordoSede = round($pesoLordoSede / $oreSede, 2);
                $resaPezzoOkSede = round($pezzoOkSede / $oreSede, 2);
                $resaValoreOkSede = round($pesoPdaOkSede / $oreSede, 2);
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

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoCartaceoSede</td>";
                $html .= "<td>$pezzoMailSede</td>";
                $html .= "<td>$percentualeInvioSede</td>";

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
                $oreTotale += round($oreSede,2);

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

                $resaPezzoLordoTotale = round($pezzoLordoTotale / $oreTotale, 2);
                $resaValoreLordoTotale = round($pesoLordoTotale / $oreTotale, 2);
                $resaPezzoOkTotale = round($pezzoOkTotale / $oreTotale, 2);
                $resaValoreOkTotale = round($pesoPdaOkTotale / $oreTotale, 2);

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

                $html .= "<td style='border-left: 5px double #D0E4F5'>$oreTotale</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$resaPezzoLordoTotale</td>";
                $html .= "<td>$resaValoreLordoTotale</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$resaPezzoOkTotale</td>";
                $html .= "<td>$resaValoreOkTotale</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoBollettinoTotale</td>";
                $html .= "<td>$pezzoRidTotale</td>";
                $html .= "<td>$percentualeBollettinoTotale</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoCartaceoTotale</td>";
                $html .= "<td>$pezzoMailTotale</td>";
                $html .= "<td>$percentualeInvioTotale</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoLuceTotale</td>";
                $html .= "<td>$pezzoGasTotale</td>";
                $html .= "<td>$pezzoDualTotale</td>";
                $html .= "<td>$pezzoPolizzaTotale</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostOkTotale</td>";
                $html .= "<td>$resaPostOKTotale</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostKoTotale</td>";
                $html .= "<td>$resaPostKOTotale</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoPostBklTotale</td>";
                $html .= "<td>$resaPostBklTotale</td>";

                $html .= "</tr>";
            } else {
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

                    $query = $queryCrm2 . $tipoCampagnaEscluso;
                    $risultatoCRM2 = $conn19->query($query);
                    $conteggio = $risultatoCRM2->num_rows;
                    //echo $conteggio;
                    if ($conteggio > 0) {
                        $rigaCRM2 = $risultatoCRM2->fetch_array();
                        $pesoLordo = round($rigaCRM2[0], 2);
                        $pesoPdaOk = round($rigaCRM2[1], 2);
                        $pesoPdaKo = round($rigaCRM2[2], 2);
                        $pesoPdaBkl = round($rigaCRM2[3], 2);
                        $pesoPdaBklp = round($rigaCRM2[4], 2);
                        $pezzoLordo = round($rigaCRM2[5], 0);
                        $pezzoOk = round($rigaCRM2[6], 0);
                        $pezzoKo = round($rigaCRM2[7], 0);
                        $pezzoBkl = round($rigaCRM2[8], 0);
                        $pezzoBklp = round($rigaCRM2[9], 0);
                        $resaPezzoLordo = 0;
                        $resaValoreLordo = 0;
                        $resaPezzoOk = 0;
                        $resaValoreOk = 0;
                        $pesoPostOk = 0;
                        $pesoPostKo = 0;
                        $pesoPostBkl = 0;
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
                        $html .= "<td>$resaPostBkl<</td>";

                        $html .= "</tr>";
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
                $oreSede += round($ore,2);

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

                $resaPezzoLordoSede = round($pezzoLordoSede / $oreSede, 2);
                $resaValoreLordoSede = round($pesoLordoSede / $oreSede, 2);
                $resaPezzoOkSede = round($pezzoOkSede / $oreSede, 2);
                $resaValoreOkSede = round($pesoPdaOkSede / $oreSede, 2);
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

                $html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoCartaceoSede</td>";
                $html .= "<td>$pezzoMailSede</td>";
                $html .= "<td>$percentualeInvioSede</td>";

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
                $oreTotale += round($oreSede,2);
                $pesoPostOkTotale += $pesoPostOkSede;
                $pesoPostKoTotale += $pesoPostKoSede;
                $pesoPostBklTotale += $pesoPostBklSede;
                $pezzoPostOkTotale += $pezzoPostOkSede;
                $pezzoPostKoTotale += $pezzoPostKoSede;
                $pezzoPostBklTotale += $pezzoPostBklSede;
                $pezzoBollettinoTotale += $pezzoBollettinoSede;
                $pezzoRidTotale += $pezzoRidSede;
                $pezzoCartaceoTotale += $pezzoCartaceoSede;
                $pezzoMailTotale += $pezzoMailSede;
                $pezzoLuceTotale += $pezzoLuceSede;
                $pezzoGasTotale += $pezzoGasSede;
                $pezzoDualTotale += $pezzoDualSede;
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
            $oreSede += round($ore,2);
            $pesoPostOkSede += $pesoPostOk;
            $pesoPostKoSede += $pesoPostKo;
            $pesoPostBklSede += $pesoPostBkl;
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
        }
    }
}
$html .= "</tr></table>";
if ($idMandato == 'Plenitude') {
    include "creaTabellaPolizze.php";
}

echo $html;

