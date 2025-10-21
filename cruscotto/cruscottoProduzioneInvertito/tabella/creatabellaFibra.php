<?php


//include "/Applications/MAMP/htdocs/Know/cruscotto/cruscottoProduzioneInvertito/creaTabellaInvertito.php";

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
$percentualeSuInseritoTotale = 0;

$pezzoBollettinoOKSede = 0;
$pezzoRidOKSede = 0;
$pezzoCartaceoOKSede = 0;
$pezzoMailOKSede = 0;

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
$pezzoPostOk = 0;
$pezzoInseritoOk = 0;
$pezzoBollettinoOKTotale = 0;
$pezzoRidOKTotale = 0;
$pezzoCartaceoOKTotale = 0;
$pezzoMailOKTotale = 0;
$sedePrecedente = "";
$pezzoOkInseritoPdp = 0;
$pezzoOkPostPdp  = 0;
$pezzoOkInseritoPdpTotale = 0;
$pezzoOkPostPdpTotale = 0;
$percentualeSuPostTotale = 0;
$pezzoOkInseritoPdpsede = 0;
$pezzoOkPostPdpsede  = 0;
$pezzoOkInseritoPdpTotale = 0;
$pezzoOkPostPdpTotale = 0;

$queryCrm = "SELECT
    sede,
    tipoCampagna,
   SUM(
    IF(
        fasePDA <> '' AND comodity = 'Fibra Enel', 
        totalePesoLordo, 
        0
    )
),
    SUM(IF(fasePDA = 'OK' AND comodity = 'Fibra Enel', totalePesoLordo, 0)),
    SUM(IF(fasePDA = 'KO' AND comodity = 'Fibra Enel', totalePesoLordo, 0)),
    SUM(IF(fasePDA = 'BKL' AND comodity = 'Fibra Enel', totalePesoLordo, 0)),
    SUM(IF(fasePDA = 'BKLP' AND comodity = 'Fibra Enel', totalePesoLordo, 0)),
    SUM(
    IF(
        fasePDA <> '' AND comodity = 'Fibra Enel',
        pezzoLordo,
        0
    )
),
    SUM(IF(fasePDA = 'OK' AND comodity = 'Fibra Enel', pezzoLordo, 0)),
    SUM(IF(fasePDA = 'KO' AND comodity = 'Fibra Enel', pezzoLordo, 0)),
    SUM(IF(fasePDA = 'BKL' AND comodity = 'Fibra Enel', pezzoLordo, 0)),
    SUM(IF(fasePDA = 'BKLP' AND comodity = 'Fibra Enel', pezzoLordo, 0)),
    SUM(IF(fasePost = 'OK' AND fasePDA = 'OK' AND comodity = 'Fibra Enel', pezzoLordo, 0)),
    SUM(IF(fasePost = 'KO' AND fasePDA = 'OK' AND comodity = 'Fibra Enel', pezzoLordo, 0)),
    SUM(IF(fasePost = 'BKL' AND fasePDA = 'OK' AND comodity = 'Fibra Enel', pezzoLordo, 0)),
    SUM(IF(fasePost = 'OK' AND comodity = 'Fibra Enel', totalePesoLordo, 0)),
    SUM(IF(fasePost = 'KO' AND comodity = 'Fibra Enel', totalePesoLordo, 0)),
    SUM(IF(fasePost = 'BKL' AND comodity = 'Fibra Enel', totalePesoLordo, 0)),
    SUM(IF(metodoPagamento = 'Bollettino Postale' AND comodity = 'Fibra Enel', pezzoLordo, 0)),
    SUM(IF(metodoPagamento = 'RID' AND comodity = 'Fibra Enel', pezzoLordo, 0)),
    SUM(IF(metodoInvio = 'Cartaceo' AND comodity = 'Fibra Enel', pezzoLordo, 0)),
    SUM(IF(metodoInvio = 'Bollettaweb' AND comodity = 'Fibra Enel', pezzoLordo, 0)),
    SUM(IF(comodity = 'Luce' AND comodity = 'Fibra Enel', pezzoLordo, 0)),
    SUM(IF(comodity = 'Gas' AND comodity = 'Fibra Enel', pezzoLordo, 0)),
    SUM(IF(comodity = 'Dual' AND comodity = 'Fibra Enel', pezzoLordo, 0)),
    SUM(IF(comodity = 'Fibra Enel', pezzoLordo, 0)),
    SUM(IF(metodoPagamento = 'Bollettino Postale' AND fasePDA = 'OK' AND comodity = 'Fibra Enel', pezzoLordo, 0)),
    SUM(IF(metodoPagamento = 'RID' AND fasePDA = 'OK' AND comodity = 'Fibra Enel', pezzoLordo, 0)),
    SUM(IF(metodoInvio = 'Cartaceo' AND fasePDA = 'OK' AND comodity = 'Fibra Enel', pezzoLordo, 0)),
    SUM(IF(metodoInvio = 'Bollettaweb' AND fasePDA = 'OK' AND comodity = 'Fibra Enel', pezzoLordo, 0)),
    SUM(IF(fasePDA = 'OK' AND comodity <> 'Fibra Enel', pezzoLordo, 0)),
    SUM(IF(fasePost = 'OK' AND fasePDA = 'OK' AND comodity <> 'Fibra Enel', pezzoLordo, 0)),
        (
        SELECT SUM(o.enelInPdp)
        FROM obbiettivoTL o
        WHERE o.sede = enelIn.sede
        AND o.mese <= DATE_FORMAT('$dataMaggiore', '%Y-%m-01')
        AND o.mese >= DATE_FORMAT('$dataMinore', '%Y-%m-01')
        AND o.tipo = 'OUT' 
    ) AS enelInPdp
FROM
    enelIn
INNER JOIN aggiuntaEnelIn ON enelIn.id = aggiuntaEnelIn.id
WHERE
    data <= '$dataMaggiore' AND data >= '$dataMinore'
     $querySede 
    AND statoPda <> 'bozza'
    AND statoPda <> 'annullata'
    AND statoPda <> 'pratica doppia'
    AND statoPda <> 'In attesa Sblocco'
    GROUP BY
    sede";
//echo $queryCrm;
$html .= "<br>";
$html .= "<table class='blueTable'>";
include "../../tabella/intestazioneTabellaFibra.php";

//echo  $queryCrm;

$risultatoCrm = $conn19->query($queryCrm);

while ($rigaCRM = $risultatoCrm->fetch_array()) {

    $sede = $rigaCRM[0];
    $sedeRicerca = ucwords($sede);
    $descrizioneMandato = $rigaCRM[1];

    $ore = 0;
    $pesoLordo = round($rigaCRM[2], 2);
    $pesoPdaOk = round($rigaCRM[3], 2);
    $pesoPdaKo = round($rigaCRM[4], 2);
    $pesoPdaBkl = round($rigaCRM[5], 2);
    $pesoPdaBklp = round($rigaCRM[6], 2);
    $pezzoLordo = round($rigaCRM[7], 0);
    $pezzoOk = round($rigaCRM[8], 0);
    $pezzoKo = round($rigaCRM[9], 0);
    $pezzoBkl = round($rigaCRM[10], 0);
    $pezzoBklp = round($rigaCRM[11], 0);
    $resaPezzoLordo = ($ore == 0) ? 0 : round($pezzoLordo / $ore, 2);
    $resaValoreLordo = ($ore == 0) ? 0 : round($pesoLordo / $ore, 2);
    $resaPezzoOk = ($ore == 0) ? 0 : round($pezzoOk / $ore, 2);
    $resaValoreOk = ($ore == 0) ? 0 : round($pesoPdaOk / $ore, 2);
    $pesoPostOk = 0;
    $pesoPostKo = 0;
    $pesoPostBkl = 0;
    $pezzoPostOk = round($rigaCRM[12], 0);
    $pezzoPostKo = round($rigaCRM[13], 0);
    $pezzoPostBkl = round($rigaCRM[14], 0);
    $resaPostOK = ($pezzoOk == 0) ? 0 : round(($pezzoPostOk / $pezzoOk) * 100, 2);
    $resaPostKO = ($pezzoOk == 0) ? 0 : round(($pezzoPostKo / $pezzoOk) * 100, 2);
    $resaPostBkl = ($pezzoOk == 0) ? 0 : round(($pezzoPostBkl / $pezzoOk) * 100, 2);
    $pezzoBollettino = round($rigaCRM[18], 0);
    $pezzoRid = round($rigaCRM[19], 0);

    $percentualeBollettino = (($pezzoBollettino + $pezzoRid) == 0) ? 0 : round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);

    $pezzoCartaceo = round($rigaCRM[20], 0);
    $pezzoMail = round($rigaCRM[21], 0);

    $percentualeInvio = (($pezzoCartaceo + $pezzoMail) == 0) ? 0 : round(($pezzoMail / ($pezzoCartaceo + $pezzoMail)) * 100, 2);

    $pezzoLuce = round($rigaCRM[22], 0);
    $pezzoGas = round($rigaCRM[23], 0);
    $pezzoDual = round($rigaCRM[24], 0);
    $pezzoPolizza = round($rigaCRM[25], 0);

    $pezzoBollettinoOK = round($rigaCRM[26], 0);
    $pezzoRidOK = round($rigaCRM[27], 0);

    $percentualeBollettinoOK = (($pezzoBollettinoOK + $pezzoRidOK) == 0) ? 0 : round((($pezzoBollettinoOK / ($pezzoBollettinoOK + $pezzoRidOK)) * 100), 2);

    $pezzoCartaceoOK = round($rigaCRM[28], 0);
    $pezzoMailOK = round($rigaCRM[29], 0);
    
    $giornirimanenti = $giorniLavorativiPrevisti - $giorniLavoratiEffettivi;
    
    
    
    $pezzoOkInseritoPdp = round($rigaCRM[30], 0);
    $pezzoOkPostPdp = round($rigaCRM[31], 0);
    $obiettivoF = round($rigaCRM[32], 0);
  
    $differenzaObj = $pezzoOk - $obiettivoF;
  
    if ($giorniLavoratiEffettivi > 0 && is_numeric($ore) && is_numeric($giorniLavorativiPrevisti)) {
    $orepaf = round((round($ore, 2) / $giorniLavoratiEffettivi) * $giorniLavorativiPrevisti, 2);
} else {
    $orepaf = 0; // Se non ci sono giorni lavorati o i valori non sono validi, le ore previste devono essere 0
}

if ($giorniLavoratiEffettivi > 0 && is_numeric($pezzoOk) && is_numeric($giorniLavorativiPrevisti)) {
    $pezzoOkPaf = round((round($pezzoOk) / $giorniLavoratiEffettivi) * $giorniLavorativiPrevisti);
} else {
    $pezzoOkPaf = 0; // Se non ci sono giorni lavorati o i valori non sono validi, i pezzi OK previsti devono essere 0
}
    
    
    
// Controllo se ci sono giorni rimanenti e se l'obiettivo è valido
if ($giornirimanenti > 0 && isset($obiettivoF) && is_numeric($obiettivoF)) {
    $passoGiorno = ($obiettivoF - $pezzoOk) / $giornirimanenti;
    
    // Se abbiamo già superato l'obiettivo, impostiamo il passo a 0
    if ($passoGiorno < 0) {
        $passoGiorno = 0;
    }
} else {
    // Se non ci sono giorni rimanenti o obiettivo non valido, passo = 0
    $passoGiorno = 0;
}

// Arrotondamento a 2 decimali per una lettura più pulita
$passoGiorno = round($passoGiorno, 2);
    
    
    
    
    $percentualeInvioOK = (($pezzoCartaceoOK + $pezzoMailOK) == 0) 
    ? 0 
    : round(($pezzoMailOK / ($pezzoCartaceoOK + $pezzoMailOK)) * 100, 2);

    $percentualeSuInserito = ($pezzoOk == 0 || $pezzoOkInseritoPdp == 0) 
    ? 0 
    : round(($pezzoOk / $pezzoOkInseritoPdp) * 100, 2);

$percentualeSuPost = ($pezzoPostOk  == 0 || $pezzoOkPostPdp == 0) 
    ? 0 
    : round(($pezzoPostOk  / $pezzoOkPostPdp) * 100, 2);

    $html .= "<tr>";
    $html .= "<td >$idMandato</td>";
    $html .= "<td >$sede</td>";

    $html .= "<td>-</td>";

    $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoLordo</td>";
    $html .= "<td>$pesoLordo</td>";

    $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoOk</td>";
    $html .= "<td>$pesoPdaOk</td>";

    $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoKo</td>";
    $html .= "<td>$pesoPdaKo</td>";

    $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoBkl</td>";
    $html .= "<td>$pesoPdaBkl</td>";

    $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoBklp</td>";
    $html .= "<td>$pesoPdaBklp</td>";

    $html .= "<td style='border-left: 2px solid lightslategray'>" . round($ore, 2) . "</td>";
    
    
    
    $html .= "<td style='border-left: 2px solid lightslategray'>$percentualeSuInserito</td>";

    $html .= "<td style='border-left: 2px solid lightslategray'>$resaPezzoLordo</td>";
    $html .= "<td>$resaValoreLordo</td>";

    $html .= "<td style='border-left: 2px solid lightslategray'>$resaPezzoOk</td>";
    $html .= "<td>$resaValoreOk</td>";

    $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoBollettino</td>";
    $html .= "<td>$pezzoRid</td>";
    $html .= "<td>$percentualeBollettino</td>";

    $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoBollettinoOK</td>";
    $html .= "<td>$pezzoRidOK</td>";
    $html .= "<td>$percentualeBollettinoOK</td>";

    $html .= "<td style='border-left: 2px solid lightslategray'>$obiettivoF</td>";
    $html .= "<td>" . round($passoGiorno, 0) . "</td>";
    $html .= "<td>$differenzaObj</td>";

    $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoCartaceoOK</td>";
    $html .= "<td>$pezzoOkPaf</td>";
    $html .= "<td>$percentualeInvioOK</td>";

    $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoLuce</td>";
    $html .= "<td>$pezzoGas</td>";
    $html .= "<td>$pezzoDual</td>";
    $html .= "<td>$pezzoPolizza</td>";
    
    
    $html .= "<td>$percentualeSuPost</td>";

    $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoPostOk</td>";
    $html .= "<td>$resaPostOK</td>";

    $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoPostKo</td>";
    $html .= "<td>$resaPostKO</td>";

    $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoPostBkl</td>";
    $html .= "<td>$resaPostBkl</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>0</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>0</td>";
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
    $oreSede += round($ore, 2);
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
    $sedePrecedente = $sede;
    $pezzoOkInseritoPdpsede += $pezzoOkInseritoPdp;
    $pezzoOkPostPdpsede += $pezzoOkPostPdp;
    $pezzoBollettinoOKSede += $pezzoBollettinoOK;
    $pezzoRidOKSede += $pezzoRidOK;
    $pezzoCartaceoOKSede += $pezzoCartaceoOK;
    $pezzoMailOKSede += $pezzoMailOK;
   
     
}



    
  
$pezzoOkInseritoPdpTotale += $pezzoOkInseritoPdpsede;
$pezzoOkPostPdpTotale += $pezzoOkPostPdpsede;

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








$pezzoBollettinoOKTotale += $pezzoBollettinoOKSede;
$pezzoRidOKTotale += $pezzoRidOKSede;
$pezzoCartaceoOKTotale += $pezzoCartaceoOKSede;
$pezzoMailOKTotale += $pezzoMailOKSede;
    
$resaPezzoLordoTotale = ($oreTotale == 0) ? 0 : round($pezzoLordoTotale / $oreTotale, 2);
$resaValoreLordoTotale = ($oreTotale == 0) ? 0 : round($pesoLordoTotale / $oreTotale, 2);
$resaPezzoOkTotale = ($oreTotale == 0) ? 0 : round($pezzoOkTotale / $oreTotale, 2);
$resaValoreOkTotale = ($oreTotale == 0) ? 0 : round($pesoPdaOkTotale / $oreTotale, 2);
$resaPostOKTotale = ($pezzoOkTotale == 0) ? 0 : round(($pezzoPostOkTotale / $pezzoOkTotale) * 100, 2);
$resaPostKOTotale = ($pezzoOkTotale == 0) ? 0 : round(($pezzoPostKoTotale / $pezzoOkTotale) * 100, 2);
$resaPostBklTotale = ($pezzoOkTotale == 0) ? 0 : round(($pezzoPostBklTotale / $pezzoOkTotale) * 100, 2);
$percentualeBollettinoTotale = (($pezzoBollettinoTotale + $pezzoRidTotale) == 0) ? 0 : round((($pezzoBollettinoTotale / ($pezzoBollettinoTotale + $pezzoRidTotale)) * 100), 2);
$percentualeInvioTotale = (($pezzoCartaceoTotale + $pezzoMailTotale) == 0) ? 0 : round(($pezzoMailTotale / ($pezzoCartaceoTotale + $pezzoMailTotale)) * 100, 2);

$percentualeBollettinoOKTotale = (($pezzoBollettinoOKTotale + $pezzoRidOKTotale) == 0) ? 0 : round((($pezzoBollettinoOKTotale / ($pezzoBollettinoOKTotale + $pezzoRidOKTotale)) * 100), 2);
$percentualeInvioOKTotale = (($pezzoCartaceoOKTotale + $pezzoMailOKTotale) == 0) ? 0 : round(($pezzoMailOKTotale / ($pezzoCartaceoOKTotale + $pezzoMailOKTotale)) * 100, 2);


    $percentualeSuInseritoTotale = ($pezzoOkTotale == 0 || $pezzoOkInseritoPdpTotale == 0) 
    ? 0 
    : round(($pezzoOkTotale / $pezzoOkInseritoPdpTotale) * 100, 2);

$percentualeSuPostTotale = ($pezzoPostOkTotale  == 0 || $pezzoOkPostPdpTotale == 0) 
    ? 0 
    : round(($pezzoPostOkTotale  / $pezzoOkPostPdpTotale) * 100, 2);


$html .= "<tr style='background-color: orangered;border: 2px solid lightslategray'>";
$html .= "<td colspan='3'>TOTALE</td>";

$html .= "<td style='border-left: 2px solid lightslategray'>$pezzoLordoTotale</td>";
$html .= "<td>$pesoLordoTotale</td>";

$html .= "<td style='border-left: 2px solid lightslategray'>$pezzoOkTotale</td>";
$html .= "<td>$pesoPdaOkTotale</td>";

$html .= "<td style='border-left: 2px solid lightslategray'>$pezzoKoTotale</td>";
$html .= "<td>$pesoPdaKoTotale</td>";

$html .= "<td style='border-left: 2px solid lightslategray'>$pezzoBklTotale</td>";
$html .= "<td>$pesoPdaBklTotale</td>";

$html .= "<td style='border-left: 2px solid lightslategray'>$pezzoBklpTotale</td>";
$html .= "<td>$pesoPdaBklpTotale</td>";

$html .= "<td style='border-left: 2px solid lightslategray'>$oreTotale</td>";


$html .= "<td style='border-left: 2px solid lightslategray'>$percentualeSuInseritoTotale</td>";

$html .= "<td style='border-left: 2px solid lightslategray'>$resaPezzoLordoTotale</td>";
$html .= "<td>$resaValoreLordoTotale</td>";

$html .= "<td style='border-left: 2px solid lightslategray'>$resaPezzoOkTotale</td>";
$html .= "<td>$resaValoreOkTotale</td>";

$html .= "<td style='border-left: 2px solid lightslategray'>$pezzoBollettinoTotale</td>";
$html .= "<td>$pezzoRidTotale</td>";
$html .= "<td>$percentualeBollettinoTotale</td>";

$html .= "<td style='border-left: 2px solid lightslategray'>$pezzoBollettinoOKTotale</td>";
$html .= "<td>$pezzoRidOKTotale</td>";
$html .= "<td>$percentualeBollettinoOKTotale</td>";

   $html .= "<td style='border-left: 2px solid lightslategray'>$obiettivoF</td>";
    $html .= "<td>" . round($passoGiorno, 0) . "</td>";
    $html .= "<td>$differenzaObj</td>";

$html .= "<td style='border-left: 2px solid lightslategray'>$pezzoCartaceoOKTotale</td>";
$html .= "<td>$pezzoOkPaf</td>";
$html .= "<td>$percentualeInvioOKTotale</td>";

$html .= "<td style='border-left: 2px solid lightslategray'>$pezzoLuceTotale</td>";
$html .= "<td>$pezzoGasTotale</td>";
$html .= "<td>$pezzoDualTotale</td>";
$html .= "<td>$pezzoPolizzaTotale</td>";

$html .= "<td style='border-left: 2px solid lightslategray'>$percentualeSuPostTotale</td>";

$html .= "<td style='border-left: 2px solid lightslategray'>$pezzoPostOkTotale</td>";
$html .= "<td>$resaPostOKTotale</td>";

$html .= "<td style='border-left: 2px solid lightslategray'>$pezzoPostKoTotale</td>";
$html .= "<td>$resaPostKOTotale</td>";

$html .= "<td style='border-left: 2px solid lightslategray'>$pezzoPostBklTotale</td>";
$html .= "<td>$resaPostBklTotale</td>";

$html .= "<td style='border-left: 2px solid lightslategray'>0</td>";
$html .= "<td>0</td>";

$html .= "</tr>";

