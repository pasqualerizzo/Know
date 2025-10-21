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
c.cf_5599 AS 'Bollettino Web',
c.cf_5619 AS 'Causale Annullamento Gas',
c.cf_5617 AS 'Causale Annullamento Luce',
c.cf_5585 AS 'Cellulare',
c.cf_5583 AS 'Codice Fiscale',
c.cf_5591 AS 'Codice PDR',
c.cf_5589 AS 'Codice POD',
c.cf_5607 AS 'Cognome',
c.cf_5631 AS 'Commodity',
c.cf_5629 AS 'Consenso Risparmiami',
e.createdtime AS 'Created Time',
c.cf_5797 AS 'Data Creazione Contratto',
en.enelin AS 'Enelin',
en.enelinno AS 'EnelIn No',
c.cf_5603 AS 'Fibra Enel',
c.cf_5615 AS 'Id Riga Gas',
c.cf_5613 AS 'Id Riga Luce',
c.cf_5597 AS 'Metodo di Pagamento',
e.modifiedby AS 'Modified By',
e.modifiedtime AS 'Modified Time',
c.cf_5605 AS 'Nome',
c.cf_5611 AS 'Note BackOffice',
c.cf_5627 AS 'Note Stato Gas',
c.cf_5625 AS 'Note Stato Luce',
c.cf_5581 AS 'Ragione Sociale',
c.cf_5623 AS 'Stato Offerta Gas',
c.cf_5621 AS 'Stato Offerta Luce',
c.cf_5609 AS 'Stato PDA',
c.cf_5595 AS 'Tariffa Gas',
c.cf_5593 AS 'Tariffa Luce',
c.cf_5587 AS 'Telefono'
FROM 
vtiger_enelincf  as c
inner join vtiger_enelin as en on c.enelInid=en.enelInid 
inner join vtiger_crmentity as e on c.enelInid=e.crmid 
inner join vtiger_users as operatore on e.smownerid=operatore.id 
WHERE
e.deleted=0 and c.cf_5797>='2025-05-30'
";

$risposta = $connCrm->query($queryOrigine);
while ($riga = $risposta->fetch_array()) {
    $operatore = $riga['operatore'];
    $bollettinoWeb = $riga['Bollettino Web'];
    $CausaleAnnullamentoGas = $riga['Causale Annullamento Gas'];
    $CausaleAnnullamentoLuce = $riga['Causale Annullamento Luce'];
    $Cellulare = $riga['Cellulare'];
    $CodiceFiscale = $riga['Codice Fiscale'];
    $CodicePDR = $riga['Codice PDR'];
    $CodicePOD = $riga['Codice POD'];
    $Cognome = $riga['Cognome'];
    $Commodity = $riga['Commodity'];
    $ConsensoRisparmiami = $riga['Consenso Risparmiami'];
    $createdTime = $riga['Created Time'];
    $DataCreazioneContratto = $riga['Data Creazione Contratto'];
    $Enelin = $riga['Enelin'];
    $EnelInNo = $riga['EnelIn No'];
    $FibraEnel = $riga['Fibra Enel'];
    $IdRigaGas = $riga['Id Riga Gas'];
    $IdRigaLuce = $riga['Id Riga Luce'];
    $MetodoPagamento = $riga['Metodo di Pagamento'];
    $modifiedBy = $riga['Modified By'];
    $modifiedTime = $riga['Modified Time'];
    $Nome = $riga['Nome'];
    $NoteBackOffice = $riga['Note BackOffice'];
    $NoteStatoGas = $riga['Note Stato Gas'];
    $NoteStatoLuce = $riga['Note Stato Luce'];
    $RagioneSociale = $riga['Ragione Sociale'];
    $StatoOffertaGas = $riga['Stato Offerta Gas'];
    $StatoOffertaLuce = $riga['Stato Offerta Luce'];
    $StatoPDA = $riga['Stato PDA'];
    $TariffaGas = $riga['Tariffa Gas'];
    $TariffaLuce = $riga['Tariffa Luce'];
    $Telefono = $riga['Telefono'];

     $queryOperatore=" SELECT id FROM vtiger_users WHERE user_name='$operatore'";
    $risp=$connCrmNuovo->query($queryOperatore);
    if($risp->num_rows>0){
        $op=$risp->fetch_array();
        $idOperatore=$op[0];
    }else{
        $idOperatore=5;
    }
    
    
    $dati = [
        'enelin' => $EnelInNo,
        'sede' => "Rende",
        'datacontratto' => $DataCreazioneContratto,
        'commodity' => $Commodity,
        'nome' => $Nome,
        'cognome' => $Cognome,
        'ragionesociale' => $RagioneSociale,
        'codicefiscale' => $CodiceFiscale,
        'cellulareprimario' => $Cellulare,
        'telefono' => $Telefono,
        'pod' => $CodicePOD,
        'codicepdr' => $CodicePDR,
        'tariffaluce' => $TariffaLuce,
        'tariffagas' => $TariffaGas,
        'metodopagamento' => $MetodoPagamento,
        'statopda' => $StatoPDA,
        'codiceplicoluce' => $IdRigaLuce,
        'codiceplicogas' => $IdRigaGas,
        'notebackoffice' => $NoteBackOffice,
        'statoplicoluce' => $StatoOffertaLuce,
        'noteplicoluce' => $NoteStatoLuce,
        'statoplicogas' => $StatoOffertaLuce,
        'noteplicogas' => $StatoOffertaGas,
    ];

    $r = importModulo($dati, $idOperatore, "Enelin");
    echo $r."<br>";
}