<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "../connessione/connessioneCrm.php";
require "../connessione/connessioneCrmNuovo.php";
require "../funzioni/funzioniCrm.php";

$objCrm = new ConnessioneCrm();
$connCrm = $objCrm->apriConnessioneCrm();

$objCrmN = new ConnessioneCrmNuovo();
$connCrmNuovo = $objCrmN->apriConnessioneCrmNuovo();

$queryOrigine = "
SELECT 
operatore.user_name as 'operatore',
e.smownerid AS assignedto,
cf.cf_932 AS bollettaweb,
cf.cf_940 AS capdifornitura,
cf.cf_948 AS capdiresidenza,
cf.cf_956 AS capfatturazione,
cf.cf_914 AS cellulare,
cf.cf_1572 AS chiamataconfermasms,
cf.cf_1256 AS codicecampagnapick,
cf.cf_900 AS codicefiscale,
cf.cf_992 AS codicefiscaletitolare,
cf.cf_892 AS codicelista,
cf.cf_894 AS codiceoperatore,
cf.cf_980 AS codicepdr,
cf.cf_3250 AS codiceplicogas,
cf.cf_3248 AS codiceplicoluce,
cf.cf_898 AS cognome,
cf.cf_886 AS commodity,
cf.cf_942 AS comunedifornitura,
cf.cf_950 AS comunediresidenza,
cf.cf_958 AS comunefatturazione,
cf.cf_982 AS consumo,
cf.cf_1022 AS contatore,
e.createdtime AS createdtime,
cf.cf_5500 AS dacaricare,
cf.cf_904 AS datadinascita,
cf.cf_920 AS dataemissione,
cf.cf_1846 AS datafirma,
cf.cf_1010 AS datainserimento,
cf.cf_1854 AS datamail,
cf.cf_1012 AS datapagamento,
cf.cf_1898 AS datascadenza,
cf.cf_926 AS datascadenzadocumento,
cf.cf_884 AS datasottoscrizionecontratto,
cf.cf_5484 AS dataswitchingas,
cf.cf_5480 AS dataswitchinluce,
cf.cf_5486 AS dataswitchoutgas,
cf.cf_5482 AS dataswitchoutluce,
cf.cf_1008 AS dettagliostatopda,
cf.cf_968 AS distributoreenergiaelettica,
cf.cf_918 AS emessoda,
c.enelno AS enelno,
cf.cf_1260 AS fornioregas,
cf.cf_1262 AS fornioregaspick,
cf.cf_962 AS fornitoreenergiaelettica,
cf.cf_976 AS fornitoregas,
cf.cf_994 AS iban,
c.enel_tks_id AS id,
cf.cf_1028 AS idnevegas,
cf.cf_1046 AS idneveluce,
cf.cf_1030 AS idrigagas,
cf.cf_1570 AS idrigagasduplicato,
cf.cf_1032 AS idrigaluce,
cf.cf_1568 AS idrigaluceduplicato,
cf.cf_5478 AS idsponsorizzate,
cf.cf_5498 AS id_pratica,
cf.cf_936 AS indirizzodifornitura,
cf.cf_944 AS indirizzodiresidenza,
cf.cf_954 AS indirizzofatturazione,
cf.cf_2594 AS interessatotelefonia,
cf.cf_1026 AS inviatogosign,
e.modifiedby AS lastmodifiedby,
cf.cf_1048 AS leadid,
cf.cf_902 AS luogodinascita,
cf.cf_916 AS mail,
cf.cf_978 AS matricolacontatore,
cf.cf_888 AS mercato,
cf.cf_964 AS mercatodiprovenienza,
cf.cf_988 AS metododipagamento,
e.modifiedtime AS modifiedtime,
cf.cf_5506 AS motivazioneko,
cf.cf_896 AS nome,
cf.cf_996 AS nomebanca,
cf.cf_990 AS nometitolare,
cf.cf_998 AS note,
cf.cf_1042 AS noteannullamentogas,
cf.cf_1044 AS noteannullamentoluce,
cf.cf_1004 AS notebackoffice,
cf.cf_1002 AS noteoperatore,
cf.cf_1016 AS noteportaleenel,
cf.cf_1038 AS notesospensionegas,
cf.cf_1040 AS notesospensioneluce,
cf.cf_5492 AS notestatogas,
cf.cf_5490 AS notestatoluce,
cf.cf_922 AS numerodocumento,
cf.cf_1564 AS numeroopt,
cf.cf_1024 AS operatorebo,
cf.cf_910 AS partitaiva,
cf.cf_970 AS pod,
cf.cf_972 AS potimp,
cf.cf_960 AS provinciadifatturazione,
cf.cf_938 AS provinciadifornitura,
cf.cf_906 AS provinciadinascita,
cf.cf_946 AS provinciadiresidenza,
cf.cf_1018 AS provvigionale,
cf.cf_908 AS ragionesociale,
cf.cf_952 AS residente,
cf.cf_1020 AS ricezionedocumentofattura,
cf.cf_890 AS sede,
e.source AS source,

cf.cf_1006 AS statopda,
cf.cf_1014 AS statoportaleenel,
cf.cf_1034 AS statorigagas,
cf.cf_1036 AS statorigaluce,
c.tags AS tags,
cf.cf_984 AS tariffagas,
cf.cf_986 AS tariffaluce,
cf.cf_912 AS telefono,
cf.cf_1574 AS tipoacquisizione,
cf.cf_924 AS tipodidocumento,
cf.cf_5488 AS utmcampagna


FROM 
vtiger_enelcf  as cf
inner join vtiger_enel as c ON c.enelid=cf.enelid
inner join vtiger_crmentity as e ON c.enelid=e.crmid 
inner join vtiger_users as operatore on e.smownerid=operatore.id 
WHERE
e.deleted=0 and cf.cf_884 BETWEEN '2025-06-01' AND '2025-06-06'
";

$risposta = $connCrm->query($queryOrigine);
while ($riga = $risposta->fetch_array()) {
    $operatore = $riga['operatore'];
    $assignedto = $riga['assignedto'];
    $bollettaweb = $riga['bollettaweb'];
    $capdifornitura = $riga['capdifornitura'];
    $capdiresidenza = $riga['capdiresidenza'];
    $capfatturazione = $riga['capfatturazione'];
    $cellulare = $riga['cellulare'];
    $chiamataconfermasms = $riga['chiamataconfermasms'];
    $codicecampagnapick = $riga['codicecampagnapick'];
    $codicefiscale = $riga['codicefiscale'];
    $codicefiscaletitolare = $riga['codicefiscaletitolare'];
    $codicelista = $riga['codicelista'];
    $codiceoperatore = $riga['codiceoperatore'];
    $codicepdr = $riga['codicepdr'];
    $codiceplicogas = $riga['codiceplicogas'];
    $codiceplicoluce = $riga['codiceplicoluce'];
    $cognome = $riga['cognome'];
    $commodity = $riga['commodity'];
    $comunedifornitura = $riga['comunedifornitura'];
    $comunediresidenza = $riga['comunediresidenza'];
    $comunefatturazione = $riga['comunefatturazione'];
    $consumo = $riga['consumo'];
    $contatore = $riga['contatore'];
    $createdtime = $riga['createdtime'];
    $dacaricare = $riga['dacaricare'];
    $datadinascita = $riga['datadinascita'];
    $dataemissione = $riga['dataemissione'];
    $datafirma = $riga['datafirma'];
    $datainserimento = $riga['datainserimento'];
    $datamail = $riga['datamail'];
    $datapagamento = $riga['datapagamento'];
    $datascadenza = $riga['datascadenza'];
    $datascadenzadocumento = $riga['datascadenzadocumento'];
    $datasottoscrizionecontratto = $riga['datasottoscrizionecontratto'];
    $dataswitchingas = $riga['dataswitchingas'];
    $dataswitchinluce = $riga['dataswitchinluce'];
    $dataswitchoutgas = $riga['dataswitchoutgas'];
    $dataswitchoutluce = $riga['dataswitchoutluce'];
    $dettagliostatopda = $riga['dettagliostatopda'];
    $distributoreenergiaelettica = $riga['distributoreenergiaelettica'];
    $emessoda = $riga['emessoda'];
    $enelno = $riga['enelno'];
    $fornioregas = $riga['fornioregas'];
    $fornioregaspick = $riga['fornioregaspick'];
    $fornitoreenergiaelettica = $riga['fornitoreenergiaelettica'];
    $fornitoregas = $riga['fornitoregas'];
    $iban = $riga['iban'];
    $id = $riga['id'];
    $idnevegas = $riga['idnevegas'];
    $idneveluce = $riga['idneveluce'];
    $idrigagas = $riga['idrigagas'];
    $idrigagasduplicato = $riga['idrigagasduplicato'];
    $idrigaluce = $riga['idrigaluce'];
    $idrigaluceduplicato = $riga['idrigaluceduplicato'];
    $idsponsorizzate = $riga['idsponsorizzate'];
    $id_pratica = $riga['id_pratica'];
    $indirizzodifornitura = $riga['indirizzodifornitura'];
    $indirizzodiresidenza = $riga['indirizzodiresidenza'];
    $indirizzofatturazione = $riga['indirizzofatturazione'];
    $interessatotelefonia = $riga['interessatotelefonia'];
    $inviatogosign = $riga['inviatogosign'];
    $lastmodifiedby = $riga['lastmodifiedby'];
    $leadid = $riga['leadid'];
    $luogodinascita = $riga['luogodinascita'];
    $mail = $riga['mail'];
    $matricolacontatore = $riga['matricolacontatore'];
    $mercato = $riga['mercato'];
    $mercatodiprovenienza = $riga['mercatodiprovenienza'];
    $metododipagamento = $riga['metododipagamento'];
    $modifiedtime = $riga['modifiedtime'];
    $motivazioneko = $riga['motivazioneko'];
    $nome = $riga['nome'];
    $nomebanca = $riga['nomebanca'];
    $nometitolare = $riga['nometitolare'];
    $note = $riga['note'];
    $noteannullamentogas = $riga['noteannullamentogas'];
    $noteannullamentoluce = $riga['noteannullamentoluce'];
    $notebackoffice = $riga['notebackoffice'];
    $noteoperatore = $riga['noteoperatore'];
    $noteportaleenel = $riga['noteportaleenel'];
    $notesospensionegas = $riga['notesospensionegas'];
    $notesospensioneluce = $riga['notesospensioneluce'];
    $notestatogas = $riga['notestatogas'];
    $notestatoluce = $riga['notestatoluce'];
    $numerodocumento = $riga['numerodocumento'];
    $numeroopt = $riga['numeroopt'];
    $operatorebo = $riga['operatorebo'];
    $partitaiva = $riga['partitaiva'];
    $pod = $riga['pod'];
    $potimp = $riga['potimp'];
    $provinciadifatturazione = $riga['provinciadifatturazione'];
    $provinciadifornitura = $riga['provinciadifornitura'];
    $provinciadinascita = $riga['provinciadinascita'];
    $provinciadiresidenza = $riga['provinciadiresidenza'];
    $provvigionale = $riga['provvigionale'];
    $ragionesociale = $riga['ragionesociale'];
    $residente = $riga['residente'];
    $ricezionedocumentofattura = $riga['ricezionedocumentofattura'];
    $sede = $riga['sede'];
    $source = $riga['source'];

    $statopda = $riga['statopda'];
    $statoportaleenel = $riga['statoportaleenel'];
    $statorigagas = $riga['statorigagas'];
    $statorigaluce = $riga['statorigaluce'];
    $tags = $riga['tags'];
    $tariffagas = $riga['tariffagas'];
    $tariffaluce = $riga['tariffaluce'];
    $telefono = $riga['telefono'];
    $tipoacquisizione = $riga['tipoacquisizione'];
    $tipodidocumento = $riga['tipodidocumento'];
    $utmcampagna = $riga['utmcampagna'];

    $queryOperatore = " SELECT id FROM vtiger_users WHERE user_name='$operatore'";
    $risp = $connCrmNuovo->query($queryOperatore);
    if ($risp->num_rows > 0) {
        $op = $risp->fetch_array();
        $idOperatore = $op[0];
    } else {
        $idOperatore = 5;
    }


    $dati = [
        'enel' => $enelno,
        'leadid' => $leadid,
        'idsponsorizzata' => $idsponsorizzate,
        'utm' => $utmcampagna,
        'codicecampagna' => $codicecampagnapick,
        'sede' => $sede,
        'datasottoscrizionecontratto' => $datasottoscrizionecontratto,
        'commodity' => $commodity,
        'mercato' => $mercato,
        'tipoacquisizione' => $tipoacquisizione,
        
        'cf_1615' => $codiceoperatore,
        'datacontratto' => $datasottoscrizionecontratto,
        'nome' => $nome,
        'cognome' => $cognome,
        'ragionesociale' => $ragionesociale,
        'luogonascita' => $luogodinascita,
        'provincianascita' => $provinciadinascita,
        'datanascita' => $datadinascita,
        'codicefiscale' => $codicefiscale,
        'partitaiva' => $partitaiva,
        'cellulareprimario' => $cellulare,
        'telefono' => $telefono,
        'mail' => $mail,
        'numerodocumento' => $numerodocumento,
        'datarilasciodocumento' => $dataemissione,
        'indirizzofornitura' => $indirizzodifornitura,
        'provinciafornitura' => $provinciadifornitura,
        'capfornitura' => $capdifornitura,
        'comunefornitura' => $comunedifornitura,
        'indirizzoresidenza' => $indirizzodiresidenza,
        'provinciaresidenza' => $provinciadiresidenza,
        'capresidenza' => $capdiresidenza,
        'comuneresidenza' => $comunediresidenza,
        'indirizzofatturazione' => $indirizzofatturazione,
        'provinciafatturazione' => $provinciadifatturazione,
        'capfatturazione' => $capfatturazione,
        'comunefatturazione' => $comunefatturazione,
        'pod' => $pod,
        'tariffaluce' => $tariffaluce,
        'fornitoreenergiaelettrica' => $fornitoreenergiaelettica,
        'consumoenergiaelettrica' => $consumo,
        'potenzaimpianto' => $potimp,
        'codicepdr' => $codicepdr,
        'tariffagas' => $tariffagas,
        'fornitoregas' => $fornioregas,
        'residente' => $residente,
        'metodopagamento' => $metododipagamento,
        'iban' => $iban,
        'ragionesocialetitolare' => $nometitolare,
        'codicefiscaletitolare' => $codicefiscaletitolare,
        'metodoinviofattura' => $ricezionedocumentofattura,
        'noteoperatore' => $noteoperatore,
        'notebackoffice' => $notebackoffice,
        'statopda' => $statopda,
        'cf_1654' => $datainserimento,
        'motivazioneko' => $motivazioneko,
        'codiceplicoluce' => $codiceplicoluce,
        'codiceplicogas' => $codiceplicogas,
        'statoplicoluce' => $statorigaluce,
        'statoplicogas' => $statorigagas,
        'noteplicoluce' => $notestatoluce,
        'noteplicogas' => $notestatogas,
        'dataswitchinluce' => $dataswitchinluce,
        'dataswitchoutluce' => $dataswitchoutluce,
        'dataswitchingas' => $dataswitchingas,
        'dataswitchoutgas' => $dataswitchoutgas,
    ];

    $r = importModulo($dati, $idOperatore, "Enel");
    echo $r . "<br>";
}