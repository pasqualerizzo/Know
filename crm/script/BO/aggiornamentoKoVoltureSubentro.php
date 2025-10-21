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
'IN ATTESA MODULO ISTANZA',
'Altro - A01 OLI a3cVi00000KWzRRIA1 in GAS2BE CT3025 - PDR associato ad altra Societa di vendita. Annullo richiesta',
'Altro - PDR/POD attivo e non nella titolarit? per utente richiedente',
'Altro - Il pdr ? in posizione non idonea alla riattivazione, occorre inserire richiesta di spostamento PM1',
'_La richiesta non ? compatibile con altre richieste in corso',
'Altro - PDR di altra s.d.v.',
'Altro - la richiesta concerne un PdR interrotto per causa altri - richiedere preventivo',
'Altro - Pdr attivo con altra sdv',
'Altro - pdr Nella titolarit? di altro UdD',
'Altro - PDR con vettoriamento apert',
'Altro - cliente rinucia',
'Altro - Pdr associato ad altra SdV',
'Altro - cliente non vuole procedere con noi e non si trattava comunque di attvazione semplice',
'Altro - Altro - pdr associato ad altra sdv',
'La richiesta non ? eseguibile (disattivazione in corso)',
'Altro - T-0000007860135 PDR NON LIBERO',
'Altro - A40 OLI a3cVi00000K4vFZIAZ in errore CT0600 - Attivazione servizio non compatibile con Attivazione Pervenuta Carta. Annullo richiesta',
'_La tipologia di richiesta non ? coerente con lo stato del POD',
'Altro - A01 OLI a3cVi000007uevDIAQ In GAS2BE CT3025 - PDR associato ad altra Societa di vendita. Annullo richiesta. Si attende completamento annullam',
'Altro - PDR non contendibile',
'La tipologia di richiesta non ? coerente con lo stato del POD',
'la richiesta non e eseguibile-Richiesta incompatibile con precedente richiesta ancora non evasa sul PDR.',
'Altro - ERRORE -CT0600 - Attivazione servizio non compatibile con Attivazione registrato Accertamenti  -  DA RISPOSTA DEL SALES DEL 14/10/2024 - Buong',
'Altro - Nella titolarit? di altro UdD',
'La voltura non ? eseguibile (Il Distributore ha risposto: La richiesta non e eseguibile - Richiesta incompatibile con le richieste in corso)',
'Altro - A01 OLI a3cVi000008FeeyIAC In GAS2BE CT3025 - PDR associato ad altra Societa di vendita. Annullo richiesta. Si attende completamento annullam',
'la tipologia di richiesta non ? coerente con lo stato del PdR',
'La richiesta non ? eseguibile',
'Altro - A01 OLI a3cVi00000JbIflIAF in errore CT0600 - Attivazione servizio non compatibile con Attivazione Pervenuta Carta. Annullo richiesta',
'Altro - A01 OLI a3cVi000009pGojIAE in GAS2BE CT3025 - PDR associato ad altra Societa di vendita. Annullo richiesta. Si attende aggiornamento sistemi',
'SWO non presente',
'Valore di PMA superiore alla soglia consentita	KO Tipo acquisizione',
'POSSIBILE FRODE',
'incongruenza tra data di accesso allimpianto e data di decorrenza dellaccesso per sostituzione',
'Richiesta avvenuta mediante ticket - Annullamenti 06/09/2024',
'ANNULLATO DA RETE',
 'La richiesta concerne un PdR interrotto per causa altri - richiedere preventivo',
 'Il POD non è attivo.',
     'Il PdR non ? attivo',
    'Il PdR non è attivo',
    'Il Richiedente ? gi? associato nel RCU al punto di prelievo oggetto della richiesta',
    'Il Richiedente è già associato nel RCU al punto di prelievo oggetto della richiesta',
    'La richiesta non è eseguibile (disattivazione in corso)',
    'La richiesta non è eseguibile',
    'La tipologia di richiesta non è coerente con lo stato del POD',
    'Il punto di prelievo non è attivo',
    'Il punto di prelievo non è attivo',
    'Altro - PDR con vettoriamento aperto',
    'Altro - PDR con vettoriamento aperto',
    'Pdr interrotto causa cliente',
    'Il punto di prelievo non ? attivo',
    'Il POD non ? attivo.',
    'Il POD non è attivo.',
    'Revoca switching per disattivazione della fornitura',
    'PdR non nella titolarità dell utente richiedente',
    'I dati del Cliente finale coincidono con quelli già presenti sul POD in RCU.',
    'Altro - Richiesta annullata per PDR associato nel RCU al punto di prelievo oggetto della richiesta',
  'I campi obbligatori nella richiesta non sono stati compilati o non sono stati correttamente compilati',         
'Altra richiesta di voltura gi? in corso sul POD.',
'La voltura non ? eseguibile (Il Distributore ha risposto: La richiesta non e\ eseguibile - Altre richieste in corso sul POD)',
'La voltura non e eseguibile (Il Distributore ha risposto: La richiesta non e\ eseguibile - Non e\ po',
'La voltura non ? eseguibile (Il Distributore ha risposto: La richiesta non e eseguibile - Altre richieste in corso sul POD)',
'ESITO IV1 KO DEFINITIVO',
'ESITO IV1 KO NECESSARIO SWITCH IN'

)";
$lista = "(701)";
$listaImport = 701;
$queryRicerca = "SELECT "
        . "plenicf.nome AS 'nome', "
        . "plenicf.cognome AS 'cognome', "
        . "plenicf.cellulareprimario AS 'cellulare', "
        . "plenicf.codicefiscale AS 'codiceFiscale', "
        . "plenicf.noteplicoluce AS 'noteStatoLuce', "
        . "plenicf.noteplicogas AS 'noteStatoGas', "
        . " plenicf.datasottoscrizionecontratto AS 'dataContratto'"
        . "FROM "
        . "vtiger_plenitude as plenicf "
        . "WHERE "
        . " plenicf.datasottoscrizionecontratto between '$dataMinore' and '$oggi' "
//. " AND plenicf.cf_3675 in $motivazioneKo";
//. " AND cf_3673='Ok Firma' "
        . " AND ((commodity='Luce' and plenicf.noteplicoluce  in $motivazioneKo) "
        . "or (commodity='Gas' and plenicf.noteplicogas  in $motivazioneKo )) ";

echo $queryRicerca;
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

        $query = "SELECT * FROM vicidial_list WHERE phone_number='$telefono' and list_id in $lista";
        $ris = $connS->query($query);
        if ($ris->num_rows > 0) {
            
        } else {
            inserisciLead($listaImport, $telefono, $nome, $cognome, $noteStatoLuce, $noteStatoGas, $codiceFiscale, $dataContratto);
        }
    }
}

$obj->chiudiConnessioneCrm();
$objS->chiudiConnessioneSiscallLead()
?>
