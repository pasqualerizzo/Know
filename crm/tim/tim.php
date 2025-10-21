<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "/Applications/MAMP/htdocs/Know/connessione/connessione_msqli_vici.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrmNuovo.php";

require "/Applications/MAMP/htdocs/Know/crm/tim/funzioniTim.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$objCrm = new ConnessioneCrmNuovo();
$connCrm = $objCrm->apriConnessioneCrmNuovo();

$dataImport = date('Y-m-d H:i:s');
$oggi = date('Y-m-d');
$ieri = date('Y-m-d', strtotime('-1 days'));
$confrontoCRM = date('Y-m-1', strtotime('-3 months'));
$provenienza = "TIM";
$tipoCampagna = "";

$arrayStatoPda = arrayStatoPda($conn19);
//print_r($arrayStatoPda);
//$arrayStatoLuce = arrayStatoLuce($conn19);
////print_r($arrayStatoLuce);
$arrayStatoFisso = arrayStatoFisso($conn19);
////print_r($arrayStatoGas);
$arrayCampagna = arrayCampagna($conn19);
////print_r($arrayCampagna);
//$arrayPesiComodity = arrayPesiComodity($conn19);
////print_r($arrayPesiComodity);
//$arrayMacroStato = arrayMacroStato($conn19);

/**
 * Query ricerca sul crm2
 */
$queryRicerca = " 
    SELECT
     operatore.user_name as 'operatore',
     
c.tim AS tim,
e.smownerid AS smownerid,
e.createdtime AS createdtime,
e.modifiedtime AS modifiedtime,
c.timno AS timno,
c.leadid AS leadid,
c.idsponsorizzata AS idsponsorizzata,
c.winback AS winback,
c.utm AS utm,
c.tipoesecuzione AS tipoesecuzione,
c.listid AS listid,
c.codicecampagna AS codicecampagna,
c.numeroentante AS numeroentante,
c.sede AS sede,
c.iniziochiamata AS iniziochiamata,
c.finechiamata AS finechiamata,
c.dataarrivolead AS dataarrivolead,
c.datasottoscrizionecontratto AS datasottoscrizionecontratto,
c.commodity AS commodity,
c.mercato AS mercato,
c.tipoacquisizione AS tipoacquisizione,
c.codicematricola AS codicematricola,
c.tipoprodotto AS tipoprodotto,
c.metodovendita AS metodovendita,
c.nome AS nome,
c.cognome AS cognome,
c.sesso AS sesso,
c.codicefiscale AS codicefiscale,
c.luogonascita AS luogonascita,
c.datanascita AS datanascita,
c.provincianascita AS provincianascita,
c.cellulareprimario AS cellulareprimario,
c.recapitoalternativo AS recapitoalternativo,
c.mail AS mail,
c.tipodocumento AS tipodocumento,
c.numerodocumento AS numerodocumento,
c.enterilasciodocumento AS enterilasciodocumento,
c.datarilasciodocumento AS datarilasciodocumento,
c.datascadenzadocumento AS datascadenzadocumento,
c.indirizzoresidenza AS indirizzoresidenza,
c.civicoresidenza AS civicoresidenza,
c.cittaresidenza AS cittaresidenza,
c.provinciaresidenza AS provinciaresidenza,
c.capresidenza AS capresidenza,
c.indirizzofornitura AS indirizzofornitura,
c.civicofornitura AS civicofornitura,
c.cittafornitura AS cittafornitura,
c.provinciafornitura AS provinciafornitura,
c.capfornitura AS capfornitura,
c.datainviomail AS datainviomail,
c.tipologiaattivazione AS tipologiaattivazione,
c.scontoattivazione AS scontoattivazione,
c.attivazionerateizzata AS attivazionerateizzata,
c.numerofissodamigrare AS numerofissodamigrare,
c.gestoretelefonicodamigrare AS gestoretelefonicodamigrare,
c.codicemigrazione AS codicemigrazione,
c.codicemigrazionesecondario AS codicemigrazionesecondario,
c.indirizzoresfornuguale AS indirizzoresfornuguale,
c.opzionewifiesicurezza AS opzionewifiesicurezza,
c.tipopromo AS tipopromo,
c.promoaggiuntiva AS promoaggiuntiva,
c.codicecartagiovani  AS codicecartagiovani ,
c.offertegiatimmobile AS offertegiatimmobile,
c.omaggio AS omaggio,
c.prodottovoucher AS prodottovoucher,
c.primaopzionegratuita AS primaopzionegratuita,
c.tipomodem AS tipomodem,
c.numerorate AS numerorate,
c.tipoattivazione AS tipoattivazione,
c.gestorediprovenienza AS gestorediprovenienza,
c.iccid AS iccid,
c.numerodicellulare AS numerodicellulare,
c.voce AS voce,
c.timflexy AS timflexy,
c.smarthome AS smarthome,
c.safewebplus AS safewebplus,
c.voceinternazionale AS voceinternazionale,
c.kittimcam AS kittimcam,
c.taglietimvisionfamily AS taglietimvisionfamily,
c.numeroprincipaleunica AS numeroprincipaleunica,
c.numeroaggiuntivounica AS numeroaggiuntivounica,
c.numeromobiletimsuper AS numeromobiletimsuper,
c.codiceiccid AS codiceiccid,
c.gestorenumeromobile AS gestorenumeromobile,
c.timvisionbox AS timvisionbox,
c.kitgooglenestmini AS kitgooglenestmini,
c.kitgooglenesthub AS kitgooglenesthub,
c.solotimvision AS solotimvision,
c.timvisionfamily AS timvisionfamily,
c.timvisionedisney AS timvisionedisney,
c.timvisionenetflix AS timvisionenetflix,
c.timvisiondazneinfinity AS timvisiondazneinfinity,
c.timvisioncalcioesportlightad AS timvisioncalcioesportlightad,
c.amazonprime AS amazonprime,
c.abbonamentoannualedilazionato AS abbonamentoannualedilazionato,
c.promorush AS promorush,
c.googleone AS googleone,
c.timvisionboxatmosphere AS timvisionboxatmosphere,
c.eccezzionalitatecnica AS eccezzionalitatecnica,
c.accettasospensione14gg AS accettasospensione14gg,
c.orariochiamataqc AS orariochiamataqc,
c.levadocumento AS levadocumento,
c.codicefiscaleintestatario AS codicefiscaleintestatario,
c.nomebanca AS nomebanca,
c.iban AS iban,
c.bic AS bic,
c.metododipagamento AS metododipagamento,
c.intestatario AS intestatario,
c.noteOperatore AS noteOperatore,
c.noteBackoffice AS noteBackoffice,
c.statopdatim AS statopdatim,
c.motivazionekotim AS motivazionekotim,
c.codiceplico AS codiceplico,
c.statoplico AS statoplico,
c.noteplico AS noteplico,
c.orariofirma AS orariofirma,
c.orarioacquisito AS orarioacquisito,
c.prechecktim AS prechecktim,
c.verificaboposttim AS verificaboposttim,
c.recalltim AS recalltim,
c.whatsapptim AS whatsapptim,
c.codiceordineccc AS codiceordineccc,
c.dataordineccc AS dataordineccc,
c.tipovenditatim AS tipovenditatim
FROM 
        vtiger_timcf as cf 
        inner join vtiger_tim as c on cf.timid=c.timid 
        inner join vtiger_crmentity as e on c.timid=e.crmid 
        inner join vtiger_users as operatore on e.smownerid=operatore.id 
        WHERE 
        c.datasottoscrizionecontratto >'2025-02-10' and  e.deleted=0 
        AND c.statopdatim not in ('Annullata','Annullato')  
";

$risultato = $connCrm->query($queryRicerca);

if ($risultato->num_rows == 0) {

} else {
    truncateTim($conn19);
    truncateAggiuntaTim($conn19);
    while ($riga = $risultato->fetch_array()) {
        $operatore = $riga["operatore"];
        $tim = $riga['tim'];
        $smownerid = $riga['smownerid'];
        $createdtime = $riga['createdtime'];
        $modifiedtime = $riga['modifiedtime'];
        $timno = $riga['timno'];
        $leadid = $riga['leadid'];
        $idsponsorizzata = $riga['idsponsorizzata'];
        $winback = $riga['winback'];
        $utm = $riga['utm'];
        $tipoesecuzione = $riga['tipoesecuzione'];
        $listid = $riga['listid'];
        $codicecampagna = $riga['codicecampagna'];
        $numeroentante = $riga['numeroentante'];
        $sede = $riga['sede'];
        $iniziochiamata = $riga['iniziochiamata'];
        $finechiamata = $riga['finechiamata'];
        $dataarrivolead = $riga['dataarrivolead'];
        $datasottoscrizionecontratto = $riga['datasottoscrizionecontratto'];
        $commodity = $riga['commodity'];
        $mercato = $riga['mercato'];
        $tipoacquisizione = $riga['tipoacquisizione'];
        $codicematricola = $riga['codicematricola'];
        $tipoprodotto = $riga['tipoprodotto'];
        $metodovendita = $riga['metodovendita'];
        $nome = $riga['nome'];
        $cognome = $riga['cognome'];
        $sesso = $riga['sesso'];
        $codicefiscale = $riga['codicefiscale'];
        $luogonascita = $riga['luogonascita'];
        $datanascita = $riga['datanascita'];
        $provincianascita = $riga['provincianascita'];
        $cellulareprimario = $riga['cellulareprimario'];
        $recapitoalternativo = $riga['recapitoalternativo'];
        $mail = $riga['mail'];
        $tipodocumento = $riga['tipodocumento'];
        $numerodocumento = $riga['numerodocumento'];
        $enterilasciodocumento = $riga['enterilasciodocumento'];
        $datarilasciodocumento = $riga['datarilasciodocumento'];
        $datascadenzadocumento = $riga['datascadenzadocumento'];
        $indirizzoresidenza = $riga['indirizzoresidenza'];
        $civicoresidenza = $riga['civicoresidenza'];
        $cittaresidenza = $riga['cittaresidenza'];
        $provinciaresidenza = $riga['provinciaresidenza'];
        $capresidenza = $riga['capresidenza'];
        $indirizzofornitura = $riga['indirizzofornitura'];
        $civicofornitura = $riga['civicofornitura'];
        $cittafornitura = $riga['cittafornitura'];
        $provinciafornitura = $riga['provinciafornitura'];
        $capfornitura = $riga['capfornitura'];
        $datainviomail = $riga['datainviomail'];
        $tipologiaattivazione = $riga['tipologiaattivazione'];
        $scontoattivazione = $riga['scontoattivazione'];
        $attivazionerateizzata = $riga['attivazionerateizzata'];
        $numerofissodamigrare = $riga['numerofissodamigrare'];
        $gestoretelefonicodamigrare = $riga['gestoretelefonicodamigrare'];
        $codicemigrazione = $riga['codicemigrazione'];
        $codicemigrazionesecondario = $riga['codicemigrazionesecondario'];
        $indirizzoresfornuguale = $riga['indirizzoresfornuguale'];
        $opzionewifiesicurezza = $riga['opzionewifiesicurezza'];
        $tipopromo = $riga['tipopromo'];
        $promoaggiuntiva = $riga['promoaggiuntiva'];
        $codicecartagiovani = $riga['codicecartagiovani'];
        $offertegiatimmobile = $riga['offertegiatimmobile'];
        $omaggio = $riga['omaggio'];
        $prodottovoucher = $riga['prodottovoucher'];
        $primaopzionegratuita = $riga['primaopzionegratuita'];
        $tipomodem = $riga['tipomodem'];
        $numerorate = $riga['numerorate'];
        $tipoattivazione = $riga['tipoattivazione'];
        $gestorediprovenienza = $riga['gestorediprovenienza'];
        $iccid = $riga['iccid'];
        $numerodicellulare = $riga['numerodicellulare'];
        $voce = $riga['voce'];
        $timflexy = $riga['timflexy'];
        $smarthome = $riga['smarthome'];
        $safewebplus = $riga['safewebplus'];
        $voceinternazionale = $riga['voceinternazionale'];
        $kittimcam = $riga['kittimcam'];
        $taglietimvisionfamily = $riga['taglietimvisionfamily'];
        $numeroprincipaleunica = $riga['numeroprincipaleunica'];
        $numeroaggiuntivounica = $riga['numeroaggiuntivounica'];
        $numeromobiletimsuper = $riga['numeromobiletimsuper'];
        $codiceiccid = $riga['codiceiccid'];
        $gestorenumeromobile = $riga['gestorenumeromobile'];
        $timvisionbox = $riga['timvisionbox'];
        $kitgooglenestmini = $riga['kitgooglenestmini'];
        $kitgooglenesthub = $riga['kitgooglenesthub'];
        $solotimvision = $riga['solotimvision'];
        $timvisionfamily = $riga['timvisionfamily'];
        $timvisionedisney = $riga['timvisionedisney'];
        $timvisionenetflix = $riga['timvisionenetflix'];
        $timvisiondazneinfinity = $riga['timvisiondazneinfinity'];
        $timvisioncalcioesportlightad = $riga['timvisioncalcioesportlightad'];
        $amazonprime = $riga['amazonprime'];
        $abbonamentoannualedilazionato = $riga['abbonamentoannualedilazionato'];
        $promorush = $riga['promorush'];
        $googleone = $riga['googleone'];
        $timvisionboxatmosphere = $riga['timvisionboxatmosphere'];
        $eccezzionalitatecnica = $riga['eccezzionalitatecnica'];
        $accettasospensione14gg = $riga['accettasospensione14gg'];
        $orariochiamataqc = $riga['orariochiamataqc'];
        $levadocumento = $riga['levadocumento'];
        $codicefiscaleintestatario = $riga['codicefiscaleintestatario'];
        $nomebanca = $riga['nomebanca'];
        $iban = $riga['iban'];
        $bic = $riga['bic'];
        $metododipagamento = $riga['metododipagamento'];
        $intestatario = $riga['intestatario'];
        $noteOperatore = $riga['noteOperatore'];
        $noteBackoffice = $riga['noteBackoffice'];
        $statopdatim = $riga['statopdatim'];
        $motivazionekotim = $riga['motivazionekotim'];
        $codiceplico = $riga['codiceplico'];
        $statoplico = $riga['statoplico'];
        $noteplico = $riga['noteplico'];
        $orariofirma = $riga['orariofirma'];
        $orarioacquisito = $riga['orarioacquisito'];
        $prechecktim = $riga['prechecktim'];
        $verificaboposttim = $riga['verificaboposttim'];
        $recalltim = $riga['recalltim'];
        $whatsapptim = $riga['whatsapptim'];
        $codiceordineccc = $riga['codiceordineccc'];
        $dataordineccc = $riga['dataordineccc'];
        $tipovenditatim = $riga['tipovenditatim'];


        $mandato = "tim";
        $idMandato = 60;

        if (array_key_exists($codicecampagna, $arrayCampagna)) {
            $idCampagna = $arrayCampagna[$codicecampagna][0];
            $tipoCampagna = $arrayCampagna[$codicecampagna][1];
            $tipoCampagna = ($tipoCampagna == "") ? "Prospect" : $tipoCampagna;
        } else {
            aggiuntaCampagna($conn19, $codicecampagna);
            $arrayCampagna = arrayCampagna($conn19);
            $idCampagna = $arrayCampagna[$codicecampagna][0];
            $tipoCampagna = $arrayCampagna[$codicecampagna][1];
            $tipoCampagna = ($tipoCampagna == "") ? "Prospect" : $tipoCampagna;
        }

        $queryInsert = "INSERT INTO `tim`"
            . "(`creatoDa`, `dataVendita`, `dataCreazione`, `pratica`, `codiceCampagna`, `statoPDA`,  `idGestioneLead`, `leadId`,motivazioneKo,codicePlico,statoPlico,tipoVendita,tipoCampagna,tipologiaAttivazione, scontoAttivazione, attivazioneRateizzata, metododipagamento, tipoattivazione) "
            . " VALUES "
            . " ( '$operatore', '$datasottoscrizionecontratto','$createdtime','$timno','$codicecampagna','$statopdatim','$idsponsorizzata','$leadid','$motivazionekotim','$codiceplico','$statoplico','$tipovenditatim','$tipoCampagna','$tipologiaattivazione','$scontoattivazione','$attivazionerateizzata','$metododipagamento', '$tipoattivazione'"
            . " )";
        echo $queryInsert;
        $conn19->query($queryInsert);

        $indiceContratto = $conn19->insert_id;
        $mese = date('Y-m-01', strtotime($datasottoscrizionecontratto));

        $pesoBase = 0;
        $pesoPiano = 0;
        $pesoPiano = 0;
        $pesoOpzione = 0;
        $pesoMobile = 0;
        $pesoPagamento = 0;
        $pesoTotaleLordo = 0;
        $pesoTotaleNetto = 0;
        $pesoPagato = 0;
        $pesoFormazione = 0;
        $pezzoLordo = 0;
        $pezzoNetto = 0;
        $pezzoPagato = 0;
        $pesoFormazione = 0;

        //pezzi
        $pezzoLordo = 1;
        $pezzoNetto = 0;
        $pezzoPagato = 0;
        $pezzoFormazione = 0;

        /**
         * Recupero Peso Comodity
         */
        $pesoBase = $tipovenditatim == "FISSO" ? 1.5 : 0;

        $pesoTotaleLordo=$pesoBase;

        /**
         * ricerca id stato PDA
         */
        $fasePDA = '';
        if (array_key_exists($statopdatim, $arrayStatoPda)) {
            $idStatoPda = $arrayStatoPda[$statopdatim][0];
            $fasePDA = $arrayStatoPda[$statopdatim][1];
//echo $idStatoPDA;
        } else {
            aggiuntaStatoPda($conn19, $statopdatim);
            $arrayStatoPda = arrayStatoPda($conn19);
        }

        $fasePlico = "";
        if (array_key_exists($statoplico, $arrayStatoFisso)) {
            $idStatoPlico = $arrayStatoFisso[$statoplico][0];
            $fasePlico = $arrayStatoFisso[$statoplico][1];

        } else {
            aggiuntaStatoFisso($conn19, $statoplico);
            $arrayStatoFisso = arrayStatoFisso($conn19);
        }

        if ($fasePDA=='OK'){
            $pesoTotaleNetto=$pesoTotaleLordo;
            $pezzoNetto=$pezzoLordo;
        }else{
            $pesoTotaleNetto=0;
            $pezzoNetto=0;
        }

        if($fasePlico=="OK"){
            $pesoPagato=$pesoTotaleNetto;
            $pezzoPagato=$pezzoNetto;
        }else{

                $pesoPagato=0;
                    $pezzoPagato=0;
        }

        $queryAggiunta = "INSERT INTO `aggiuntaTim`(`id`, `mese`, `pesoBase`, `pesoPiano`, `pesoOpzione`, `pesoMobile`,"
            . " `pesoPagamento`, `pesoTotaleLordo`, `pesoTotaleNetto`, `pesoPagato`, `pesoFormazione`, `pezzoLordo`,"
            . " `pezzoNetto`, `pezzoPagato`, `pezzoFormazione`, `fasePDA`,fasePost) "
            . " VALUES "
            . " ('$indiceContratto','$mese',$pesoBase,'$pesoPiano','$pesoOpzione','$pesoMobile',"
            . " '$pesoPagamento','$pesoTotaleLordo','$pesoTotaleNetto','$pesoPagato','$pesoFormazione','$pezzoLordo',"
            . " '$pezzoNetto','$pezzoPagato','$pezzoFormazione','$fasePDA','$fasePlico')";
        //echo $queryAggiunta;
        $conn19->query($queryAggiunta);

    }
}


$obj19->chiudiConnessione();
$objCrm->chiudiConnessioneCrm();

