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
cf.cf_4272 AS assegnatominipda,
e.smownerid AS assignedto,
cf.cf_4330 AS bollettaweb,
cf.cf_4394 AS bundle,
cf.cf_4352 AS capfatturazione,
cf.cf_4336 AS capfornitura,
cf.cf_4344 AS capresidenza,
cf.cf_4306 AS cellulareprimario,
cf.cf_4288 AS codmatricola,
cf.cf_4284 AS codicecampagna,
cf.cf_4298 AS codicefiscale,
cf.cf_4384 AS codicefiscaletitolare,
cf.cf_4370 AS codicepdr,
cf.cf_4292 AS cognome,
cf.cf_4278 AS commodity,
cf.cf_4354 AS comunefatturazione,
cf.cf_4338 AS comunefornitura,
cf.cf_4346 AS comuneresidenza,
cf.cf_4364 AS consumoannuo,
cf.cf_4376 AS consumoannuogas,
e.smcreatorid AS createdby,
e.createdtime AS createdtime,
cf.cf_4302 AS datadinascita,
cf.cf_4324 AS dataemissionedocumento,
cf.cf_4322 AS datascadenzadocumento,
cf.cf_4276 AS datasottoscrizionecontratto,
e.description AS description,
cf.cf_4316 AS enterilascio,
cf.cf_4362 AS fornitoreenergiaelettrica,
cf.cf_4374 AS fornitoregas,
cf.cf_4264 AS hlriren,
cf.cf_4380 AS iban,
cf.cf_4402 AS idrigagas,
cf.cf_4400 AS idrigaluce,
cf.cf_4268 AS idsponsorizzata,
cf.cf_4348 AS indirizzofatturazione,
cf.cf_4332 AS indirizzofornitura,
cf.cf_4340 AS indirizzoresidenza,
cf.cf_4262 AS interessatotelefonia,
cf.cf_4398 AS inviofattura,
c.irenno AS irenno,
cf.cf_4270 AS leadid,
cf.cf_4296 AS luogodinascita,
cf.cf_4312 AS mail,
cf.cf_4280 AS mercato,
cf.cf_4368 AS mercatodiprovenienza,
cf.cf_4378 AS metododipagamento,
e.modifiedby AS modifiedby,
e.modifiedtime AS modifiedtime,
cf.cf_4408 AS motivazioneko,
c.name AS name,
cf.cf_4328 AS nazionedinascita,
cf.cf_4290 AS nome,
cf.cf_4390 AS notebackoffice,
cf.cf_4388 AS noteoperatore,
cf.cf_4386 AS notepagamento,
cf.cf_4318 AS numerodocumento,
cf.cf_4266 AS operatoretelemarketing,
cf.cf_4304 AS partitaiva,
cf.cf_4358 AS pod,
cf.cf_4366 AS potimp,
cf.cf_4396 AS prodottobundle,
cf.cf_4300 AS provinciadinascita,
cf.cf_4350 AS provinciafatturazione,
cf.cf_4334 AS provinciafornitura,
cf.cf_4342 AS provinciaresidenza,
cf.cf_4294 AS ragionesociale,
cf.cf_4382 AS ragionesocialetitolare,
cf.cf_4310 AS recapitoalternativo,
cf.cf_4356 AS residente,
cf.cf_4282 AS sede,
cf.cf_4326 AS sesso,
e.source AS source,

cf.cf_4392 AS statopda,
cf.cf_4412 AS statorigagas,
cf.cf_4414 AS statorigaluce,
c.tags AS tags,
cf.cf_4372 AS tariffagas,
cf.cf_4360 AS tariffaluce,
cf.cf_4308 AS telefono,
cf.cf_4286 AS tipoacquisizione,
cf.cf_4314 AS tipodocumento,
cf.cf_4410 AS titolarita,
cf.cf_4274 AS winback



FROM 
vtiger_irencf  as cf
inner join vtiger_iren as c ON c.irenid=cf.irenid
inner join vtiger_crmentity as e ON c.irenid=e.crmid 
inner join vtiger_users as operatore on e.smownerid=operatore.id 
WHERE
e.deleted=0 and cf.cf_4276 BETWEEN '2025-06-01' AND '2025-06-06'
";

$risposta = $connCrm->query($queryOrigine);
while ($riga = $risposta->fetch_array()) {
    $operatore = $riga['operatore'];
    $assegnatominipda = $riga['assegnatominipda'];
    $assignedto = $riga['assignedto'];
    $bollettaweb = $riga['bollettaweb'];
    $bundle = $riga['bundle'];
    $capfatturazione = $riga['capfatturazione'];
    $capfornitura = $riga['capfornitura'];
    $capresidenza = $riga['capresidenza'];
    $cellulareprimario = $riga['cellulareprimario'];
    $codmatricola = $riga['codmatricola'];
    $codicecampagna = $riga['codicecampagna'];
    $codicefiscale = $riga['codicefiscale'];
    $codicefiscaletitolare = $riga['codicefiscaletitolare'];
    $codicepdr = $riga['codicepdr'];
    $cognome = $riga['cognome'];
    $commodity = $riga['commodity'];
    $comunefatturazione = $riga['comunefatturazione'];
    $comunefornitura = $riga['comunefornitura'];
    $comuneresidenza = $riga['comuneresidenza'];
    $consumoannuo = $riga['consumoannuo'];
    $consumoannuogas = $riga['consumoannuogas'];
    $createdby = $riga['createdby'];
    $createdtime = $riga['createdtime'];
    $datadinascita = $riga['datadinascita'];
    $dataemissionedocumento = $riga['dataemissionedocumento'];
    $datascadenzadocumento = $riga['datascadenzadocumento'];
    $datasottoscrizionecontratto = $riga['datasottoscrizionecontratto'];
    $description = $riga['description'];
    $enterilascio = $riga['enterilascio'];
    $fornitoreenergiaelettrica = $riga['fornitoreenergiaelettrica'];
    $fornitoregas = $riga['fornitoregas'];
    $hlriren = $riga['hlriren'];
    $iban = $riga['iban'];
    $idrigagas = $riga['idrigagas'];
    $idrigaluce = $riga['idrigaluce'];
    $idsponsorizzata = $riga['idsponsorizzata'];
    $indirizzofatturazione = $riga['indirizzofatturazione'];
    $indirizzofornitura = $riga['indirizzofornitura'];
    $indirizzoresidenza = $riga['indirizzoresidenza'];
    $interessatotelefonia = $riga['interessatotelefonia'];
    $inviofattura = $riga['inviofattura'];
    $irenno = $riga['irenno'];
    $leadid = $riga['leadid'];
    $luogodinascita = $riga['luogodinascita'];
    $mail = $riga['mail'];
    $mercato = $riga['mercato'];
    $mercatodiprovenienza = $riga['mercatodiprovenienza'];
    $metododipagamento = $riga['metododipagamento'];
    $modifiedby = $riga['modifiedby'];
    $modifiedtime = $riga['modifiedtime'];
    $motivazioneko = $riga['motivazioneko'];
    $name = $riga['name'];
    $nazionedinascita = $riga['nazionedinascita'];
    $nome = $riga['nome'];
    $notebackoffice = $riga['notebackoffice'];
    $noteoperatore = $riga['noteoperatore'];
    $notepagamento = $riga['notepagamento'];
    $numerodocumento = $riga['numerodocumento'];
    $operatoretelemarketing = $riga['operatoretelemarketing'];
    $partitaiva = $riga['partitaiva'];
    $pod = $riga['pod'];
    $potimp = $riga['potimp'];
    $prodottobundle = $riga['prodottobundle'];
    $provinciadinascita = $riga['provinciadinascita'];
    $provinciafatturazione = $riga['provinciafatturazione'];
    $provinciafornitura = $riga['provinciafornitura'];
    $provinciaresidenza = $riga['provinciaresidenza'];
    $ragionesociale = $riga['ragionesociale'];
    $ragionesocialetitolare = $riga['ragionesocialetitolare'];
    $recapitoalternativo = $riga['recapitoalternativo'];
    $residente = $riga['residente'];
    $sede = $riga['sede'];
    $sesso = $riga['sesso'];
    $source = $riga['source'];
    
    $statopda = $riga['statopda'];
    $statorigagas = $riga['statorigagas'];
    $statorigaluce = $riga['statorigaluce'];
    $tags = $riga['tags'];
    $tariffagas = $riga['tariffagas'];
    $tariffaluce = $riga['tariffaluce'];
    $telefono = $riga['telefono'];
    $tipoacquisizione = $riga['tipoacquisizione'];
    $tipodocumento = $riga['tipodocumento'];
    $titolarita = $riga['titolarita'];
    $winback = $riga['winback'];

    $queryOperatore = " SELECT id FROM vtiger_users WHERE user_name='$operatore'";
    $risp = $connCrmNuovo->query($queryOperatore);
    if ($risp->num_rows > 0) {
        $op = $risp->fetch_array();
        $idOperatore = $op[0];
    } else {
        $idOperatore = 5;
    }


    $dati = [

'capfatturazione'=> $capfatturazione,
'capfornitura'=> $capfornitura,
'capresidenza'=> $capresidenza,
'cellulareprimario'=> $cellulareprimario,
'codicecampagna'=> $codicecampagna,
'codicefiscale'=> $codicefiscale,
'codicefiscaletitolare'=> $codicefiscaletitolare,


'codicepdr'=> $codicepdr,
'codiceplicogas'=> $idrigagas,
'codiceplicoluce'=> $idrigaluce,
'cognome'=> $cognome,
'commodity'=> $commodity,
'comunefatturazione'=> $comunefatturazione,
'comunefornitura'=> $comunefatturazione,
'comuneresidenza'=> $comuneresidenza,
'consumoannuogas'=> $consumoannuogas,
'consumoenergiaelettrica'=> $consumoannuo,



'datacontratto'=> $datasottoscrizionecontratto,
'datanascita'=> $datadinascita,
'datarilasciodocumento'=> $dataemissionedocumento,
'datasottoscrizionecontratto'=> $datasottoscrizionecontratto,

'enterilasciodocumento'=> $enterilascio,

'fornitoreenergiaelettrica'=> $fornitoreenergiaelettrica,
'fornitoregas'=> $fornitoregas,
'iban'=> $iban,
'idsponsorizzata'=> $idsponsorizzata,
'indirizzofatturazione'=> $indirizzofatturazione,
'indirizzofornitura'=> $indirizzofornitura,
'indirizzoresidenza'=> $indirizzoresidenza,

'iren'=> $irenno,


'leadid'=> $leadid,

'luogonascita'=> $luogodinascita,
'mail'=> $mail,
'mercato'=> $mercato,
'metodoinviofattura'=> $inviofattura,
'metodopagamento'=> $metododipagamento,

'motivazioneko'=> $motivazioneko,
'nazionalita'=> $nazionedinascita,
'nome'=> $nome,
'notebackoffice'=> $notebackoffice,
'noteoperatore'=> $noteoperatore,

'numerodocumento'=> $numerodocumento,



'partitaiva'=> $partitaiva,
'pod'=> $pod,
'potenzaimpianto'=> $potimp,

'provincianascita'=> $provinciadinascita,
'provinciafatturazione'=> $provinciafatturazione,
'provinciafornitura'=> $provinciafornitura,
'provinciaresidenza'=> $provinciaresidenza,
'ragionesociale'=> $ragionesociale,
'ragionesocialetitolare'=> $ragionesocialetitolare,

'recapitoalternativo'=> $recapitoalternativo,
'residente'=> $residente,
'sede'=> $sede,
'sesso'=> $sesso,
'statopda'=> $statopda,
'statoplicogas'=> $statorigagas,
'statoplicoluce'=> $statorigaluce,
'tariffagas'=> $tariffagas,
'tariffaluce'=> $tariffaluce,
'telefono'=> $telefono,
'tipoacquisizione'=> $tipoacquisizione,






'winback'=> $winback,

    ];

    $r = importModulo($dati, $idOperatore, "Iren");
    echo $r . "<br>";
}