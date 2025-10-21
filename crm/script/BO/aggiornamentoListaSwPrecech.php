<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrmNuovo.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscallLead.php";
require "/Applications/MAMP/htdocs/Know/funzioni/funzioniSiscall.php";

$objS = new connessioneSiscallLead();
$connS = $objS->apriConnessioneSiscallLead();

$obj = new ConnessioneCrmNuovo();
$connCrm = $obj->apriConnessioneCrmNuovo();

/**
 * Finestra di verifica 3 mesi
 */
$oggi = date('Y-m-d');
$dataMinore = date('Y-m-d', strtotime("-3 months"));

$contrattiInseriti = [];

$motivazioneKo = "(
    'PdR abbinato ad un cliente finale diverso da quello dichiarato nella richiesta',
    'POD non nella titolarità dell\'utente richiedente',
    'Il codice PdR non esiste',
    'Altro - PdR non nella titolarità del utente richiedente',
    'Note Stato Luce (PRECHECK KO PER DATI ANAGRAFICI ERRATI) viene Rimosso',
    'Il PdR non è valido. Impossibile controllare richiesta.',
    'POD non valido. Impossibile controllare richiesta.',
    'Revoca switching per variazione del cliente finale sulla fornitura',
    'PRECHECK KO PER DATI ANAGRAFICI ERRATI',
    'PRECHECK KO PER MOTIVI TECNICI',
    'Altro - Richiesta inserita su POD errato',
    'LA COPPIA CODICE PDR - MATRICOLA MISURATORE NON È CONGRUENTE',
    'Incongruenza nei dati trasmessi dai presenti su pod (CAP)',
    'POD NON DI COMPETENZA DEL DISTRIBUTORE',
    'Il cliente finale è diverso da quello associato al punto di prelievo in RCU',
    'IL CODICE POD NON ESISTE',
   'La tipologia di richiesta non è coerente con lo stato del POD',
    'PdR non nella titolarit? dell?utente richiedente',
    'PdR non nella titolarità dell utente richiedente',
    'CF KO',
    'POD non nella titolarit? dell utente richiedente',
    'Altro - PdR non nella titolarita dell utente richiedente',
    'Altro - PDR INSISTENTE',
    'Il POD ? inesistente',
     'Il cliente finale ? diverso da quello associato al punto di prelievo in RCU',
     'Il POD è insistente?',
    'PRECHECK KO DEFINITIVO',
    'Il POD è inesistente',
    'La richiesta di switching non è valida in RCU.',
    'POD non nella titolarità dell\utente richiedente',
    'POD non nella titolarità dell utente richiedente'
    
   
)";

$tipoAcquisizione = "('Subentro')";
$lista = "(801)";
$listaImport = 801;
$queryRicerca = "SELECT "
      . "plenicf.nome AS 'nome', "
        . "plenicf.cognome AS 'cognome', "
        . "plenicf.cellulareprimario AS 'cellulare', "
        . "plenicf.codicefiscale AS 'codiceFiscale', "
        . "plenicf.noteplicoluce AS 'noteStatoLuce', "
        . "plenicf.noteplicogas AS 'noteStatoGas', "
        . " plenicf.datasottoscrizionecontratto AS 'dataContratto',"
        . " plenicf.pod AS 'pod', "
        . " plenicf.codicepdr AS 'pdr'"
        . "FROM "
        . "vtiger_plenitude as plenicf "
        . "WHERE "
        . " plenicf.datasottoscrizionecontratto between '$dataMinore' and '$oggi' "
//. " AND plenicf.cf_3675 in $motivazioneKo";
//. " AND cf_3673='Ok Firma' "
        . " AND ((commodity='Luce' and noteplicoluce  in $motivazioneKo) "
        . "or (commodity='Gas' and noteplicogas  in $motivazioneKo )) "
        . " and tipoacquisizione not in $tipoAcquisizione";

//echo $queryRicerca;
$risultato = $connCrm->query($queryRicerca);
if ($risultato->num_rows > 0) {
    while ($riga = $risultato->fetch_array()) {
        $telefono = $riga['cellulare'];
        $nome = $riga['nome'];
        $cognome = $riga['cognome'];
        $noteStatoLuce = $riga['noteStatoLuce'];
        $noteStatoGas = $riga['noteStatoGas'];
        $codiceFiscale = $riga['codiceFiscale'];
        $dataContratto = $riga['dataContratto'];
        $pod=$riga['pod'];
        $pdr=$riga['pdr'];

        $query = "SELECT * FROM vicidial_list WHERE phone_number='$telefono' and list_id in $lista";
        $ris = $connS->query($query);
        if ($ris->num_rows > 0) {
            
        } else {
            inserisciLeadPDP($listaImport, $telefono, $nome, $cognome, $noteStatoLuce, $noteStatoGas, $codiceFiscale, $dataContratto,$pod,$pdr);
        }
    }
}

$obj->chiudiConnessioneCrm();
$objS->chiudiConnessioneSiscallLead()
?>
