<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "/Applications/MAMP/htdocs/Know/connessione/connessione_msqli_vici.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrm.php";
require "/Applications/MAMP/htdocs/Know/funzioni/funzioniPlenitude.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$objCrm = new ConnessioneCrm();
$connCrm = $objCrm->apriConnessioneCrm();

$sincro=[];

truncateSincronizzazione($conn19);

$queryRicerca = "SELECT * FROM `sincroVivigas` ";

$risultato = $conn19->query($queryRicerca);
/**
 * Se la ricerca da errore segna nei log l'errore
 */
if (!$risultato) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $connCrm->real_escape_string($connCrm->error);
    echo $errore;
}
/**
 * se non da errore svuota la tabella, segnando nei log l'operazione, e la ricarica riga per riga da zero
 */ else {


    /**
     * Recupero informazione CRM
     */
    while ($riga = $risultato->fetch_array()) {

        $pratica = $riga['pratica'];
        $codiceFiscale = $riga['codiceFiscale'];
        $comodity = $riga['comodity'];
        $offerta = $riga['offerta'];
        $dataStipula = date('Y-m-d', strtotime(strtr($riga['dataStipula'], '/', '-')));
        $metodoPagamento = $riga['metodoPagamento'];
        $statoPDA = $riga['statoPDA'];
        $statoPost = $conn19->real_escape_string($riga['statoPost']);
        $noteStato = $conn19->real_escape_string($riga['noteStato']);
        $mandato = $conn19->real_escape_string($riga['mandato']);
        $dataImport = date('Y-m-d', strtotime(strtr($riga['dataImport'], '/', '-')));    
        
        

        $queryInserimento = "INSERT INTO `sincronizzazione`"
                . "( `dataStipula`, `comodity`,  `metodoPagamento`, `statoPDA`, `statoPost`, `noteStato`, `dataImport`, `mandato`,codiceFiscale,pratica,offerta) "
                . " VALUES "
                . " ('$dataStipula','$comodity','$metodoPagamento','$statoPDA','$statoPost','$noteStato','$dataImport','$mandato','$codiceFiscale','$pratica','$offerta')";

        $conn19->query($queryInserimento);
    }
}


$queryRicerca = "SELECT * FROM `sincroEnel` ";

$risultato = $conn19->query($queryRicerca);
/**
 * Se la ricerca da errore segna nei log l'errore
 */
if (!$risultato) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $connCrm->real_escape_string($connCrm->error);
    echo $errore;
}
/**
 * se non da errore svuota la tabella, segnando nei log l'operazione, e la ricarica riga per riga da zero
 */ else {


    /**
     * Recupero informazione CRM
     */
    while ($riga = $risultato->fetch_array()) {

        $pratica = $riga['pratica'];
        $codiceFiscale = $riga['codiceFiscale'];
        $comodity = $riga['comodity'];
        $offerta = $riga['offerta'];
        $dataStipula = date('Y-m-d', strtotime(strtr($riga['dataStipula'], '/', '-')));
        $metodoPagamento = $riga['metodoPagamento'];
        $statoPDA = $riga['statoPDA'];
        $statoPost = $conn19->real_escape_string($riga['statoPost']);
        $noteStato = $conn19->real_escape_string($riga['noteStato']);
        $mandato = $conn19->real_escape_string($riga['mandato']);
        $dataImport = date('Y-m-d', strtotime(strtr($riga['dataImport'], '/', '-')));    
        
        

        $queryInserimento = "INSERT INTO `sincronizzazione`"
                . "( `dataStipula`, `comodity`,  `metodoPagamento`, `statoPDA`, `statoPost`, `noteStato`, `dataImport`, `mandato`,codiceFiscale,pratica,offerta) "
                . " VALUES "
                . " ('$dataStipula','$comodity','$metodoPagamento','$statoPDA','$statoPost','$noteStato','$dataImport','$mandato','$codiceFiscale','$pratica','$offerta')";

        $conn19->query($queryInserimento);
    }
}
    
$queryRicerca = "SELECT * FROM `sincroPlenitude` ";

$risultato = $conn19->query($queryRicerca);
/**
 * Se la ricerca da errore segna nei log l'errore
 */
if (!$risultato) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $connCrm->real_escape_string($connCrm->error);
    echo $errore;
}
/**
 * se non da errore svuota la tabella, segnando nei log l'operazione, e la ricarica riga per riga da zero
 */ else {


    /**
     * Recupero informazione CRM
     */
    while ($riga = $risultato->fetch_array()) {

        $pratica = $riga['pratica'];
        $codiceFiscale = $riga['codiceFiscale'];
        $comodity = $riga['comodity'];
        $offerta = $conn19->real_escape_string($riga['offerta']);
        $dataStipula = date('Y-m-d', strtotime(strtr($riga['dataStipula'], '/', '-')));
        $metodoPagamento = $riga['metodoPagamento'];
        $statoPDA = $riga['statoPDA'];
        $statoPost = $conn19->real_escape_string($riga['statoPost']);
        $noteStato = $conn19->real_escape_string($riga['noteStato']);
        $mandato = $conn19->real_escape_string($riga['mandato']);
        $dataImport = date('Y-m-d', strtotime(strtr($riga['dataImport'], '/', '-')));    
        
        

        $queryInserimento = "INSERT INTO `sincronizzazione`"
                . "( `dataStipula`, `comodity`,  `metodoPagamento`, `statoPDA`, `statoPost`, `noteStato`, `dataImport`, `mandato`,codiceFiscale,pratica,offerta) "
                . " VALUES "
                . " ('$dataStipula','$comodity','$metodoPagamento','$statoPDA','$statoPost','$noteStato','$dataImport','$mandato','$codiceFiscale','$pratica','$offerta')";

        $conn19->query($queryInserimento);
    }
}


$queryRicerca = "SELECT * FROM `sincroVivigas` ";

$risultato = $conn19->query($queryRicerca);
/**
 * Se la ricerca da errore segna nei log l'errore
 */
if (!$risultato) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $connCrm->real_escape_string($connCrm->error);
    echo $errore;
}
/**
 * se non da errore svuota la tabella, segnando nei log l'operazione, e la ricarica riga per riga da zero
 */ else {


    /**
     * Recupero informazione CRM
     */
    while ($riga = $risultato->fetch_array()) {

        $pratica = $riga['pratica'];
        $codiceFiscale = $riga['codiceFiscale'];
        $comodity = $riga['comodity'];
        $offerta = $riga['offerta'];
        $dataStipula = date('Y-m-d', strtotime(strtr($riga['dataStipula'], '/', '-')));
        $metodoPagamento = $riga['metodoPagamento'];
        $statoPDA = $riga['statoPDA'];
        $statoPost = $conn19->real_escape_string($riga['statoPost']);
        $noteStato = $conn19->real_escape_string($riga['noteStato']);
        $mandato = $conn19->real_escape_string($riga['mandato']);
        $dataImport = date('Y-m-d', strtotime(strtr($riga['dataImport'], '/', '-')));    
        
        

        $queryInserimento = "INSERT INTO `sincronizzazione`"
                . "( `dataStipula`, `comodity`,  `metodoPagamento`, `statoPDA`, `statoPost`, `noteStato`, `dataImport`, `mandato`,codiceFiscale,pratica,offerta) "
                . " VALUES "
                . " ('$dataStipula','$comodity','$metodoPagamento','$statoPDA','$statoPost','$noteStato','$dataImport','$mandato','$codiceFiscale','$pratica','$offerta')";

        $conn19->query($queryInserimento);
    }
}


$queryRicerca = "SELECT * FROM `sincroEnelin` ";

$risultato = $conn19->query($queryRicerca);
/**
 * Se la ricerca da errore segna nei log l'errore
 */
if (!$risultato) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $connCrm->real_escape_string($connCrm->error);
    echo $errore;
}
/**
 * se non da errore svuota la tabella, segnando nei log l'operazione, e la ricarica riga per riga da zero
 */ else {


    /**
     * Recupero informazione CRM
     */
    while ($riga = $risultato->fetch_array()) {

        $pratica = $riga['pratica'];
        $codiceFiscale = $riga['codiceFiscale'];
        $comodity = $riga['comodity'];
        $offerta = $riga['offerta'];
        $dataStipula = date('Y-m-d', strtotime(strtr($riga['dataStipula'], '/', '-')));
        $metodoPagamento = $riga['metodoPagamento'];
        $statoPDA = $riga['statoPDA'];
        $statoPost = $conn19->real_escape_string($riga['statoPost']);
        $noteStato = $conn19->real_escape_string($riga['noteStato']);
        $mandato = $conn19->real_escape_string($riga['mandato']);
        $dataImport = date('Y-m-d', strtotime(strtr($riga['dataImport'], '/', '-')));    
        
        

        $queryInserimento = "INSERT INTO `sincronizzazione`"
                . "( `dataStipula`, `comodity`,  `metodoPagamento`, `statoPDA`, `statoPost`, `noteStato`, `dataImport`, `mandato`,codiceFiscale,pratica,offerta) "
                . " VALUES "
                . " ('$dataStipula','$comodity','$metodoPagamento','$statoPDA','$statoPost','$noteStato','$dataImport','$mandato','$codiceFiscale','$pratica','$offerta')";

        $conn19->query($queryInserimento);
    }
}


$queryRicerca = "SELECT * FROM `sincroHeracom` ";

$risultato = $conn19->query($queryRicerca);
/**
 * Se la ricerca da errore segna nei log l'errore
 */
if (!$risultato) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $connCrm->real_escape_string($connCrm->error);
    echo $errore;
}
/**
 * se non da errore svuota la tabella, segnando nei log l'operazione, e la ricarica riga per riga da zero
 */ else {


    /**
     * Recupero informazione CRM
     */
    while ($riga = $risultato->fetch_array()) {

        $pratica = $riga['pratica'];
        $codiceFiscale = $riga['codiceFiscale'];
        $comodity = $riga['comodity'];
        $offerta = $riga['offerta'];
        $dataStipula = date('Y-m-d', strtotime(strtr($riga['dataStipula'], '/', '-')));
        $metodoPagamento = $riga['metodoPagamento'];
        $statoPDA = $riga['statoPDA'];
        $statoPost = $conn19->real_escape_string($riga['statoPost']);
        $noteStato = $conn19->real_escape_string($riga['noteStato']);
        $mandato = $conn19->real_escape_string($riga['mandato']);
        $dataImport = date('Y-m-d', strtotime(strtr($riga['dataImport'], '/', '-')));    
        
        

        $queryInserimento = "INSERT INTO `sincronizzazione`"
                . "( `dataStipula`, `comodity`,  `metodoPagamento`, `statoPDA`, `statoPost`, `noteStato`, `dataImport`, `mandato`,codiceFiscale,pratica,offerta) "
                . " VALUES "
                . " ('$dataStipula','$comodity','$metodoPagamento','$statoPDA','$statoPost','$noteStato','$dataImport','$mandato','$codiceFiscale','$pratica','$offerta')";

        $conn19->query($queryInserimento);
    }
}

$queryRicerca = "SELECT * FROM `sincroOndapiu` ";

$risultato = $conn19->query($queryRicerca);
/**
 * Se la ricerca da errore segna nei log l'errore
 */
if (!$risultato) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $connCrm->real_escape_string($connCrm->error);
    echo $errore;
}
/**
 * se non da errore svuota la tabella, segnando nei log l'operazione, e la ricarica riga per riga da zero
 */ else {


    /**
     * Recupero informazione CRM
     */
    while ($riga = $risultato->fetch_array()) {

        $pratica = $riga['pratica'];
        $codiceFiscale = $riga['codiceFiscale'];
        $comodity = $riga['comodity'];
        $offerta = $riga['offerta'];
        $dataStipula = date('Y-m-d', strtotime(strtr($riga['dataStipula'], '/', '-')));
        $metodoPagamento = $riga['metodoPagamento'];
        $statoPDA = $riga['statoPDA'];
        $statoPost = $conn19->real_escape_string($riga['statoPost']);
        $noteStato = $conn19->real_escape_string($riga['noteStato']);
        $mandato = $conn19->real_escape_string($riga['mandato']);
        $dataImport = date('Y-m-d', strtotime(strtr($riga['dataImport'], '/', '-')));    
        
        

        $queryInserimento = "INSERT INTO `sincronizzazione`"
                . "( `dataStipula`, `comodity`,  `metodoPagamento`, `statoPDA`, `statoPost`, `noteStato`, `dataImport`, `mandato`,codiceFiscale,pratica,offerta) "
                . " VALUES "
                . " ('$dataStipula','$comodity','$metodoPagamento','$statoPDA','$statoPost','$noteStato','$dataImport','$mandato','$codiceFiscale','$pratica','$offerta')";

        $conn19->query($queryInserimento);
    }
}






$queryRicerca = "SELECT * FROM `sincronizzazione` ";

$risultato = $conn19->query($queryRicerca);
/**
 * Se la ricerca da errore segna nei log l'errore
 */
if (!$risultato) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $connCrm->real_escape_string($connCrm->error);
    echo $errore;
}
/**
 * se non da errore svuota la tabella, segnando nei log l'operazione, e la ricarica riga per riga da zero
 */ else {


    /**
     * Recupero informazione CRM
     */
    while ($riga = $risultato->fetch_array()) {

        $pratica = $riga['pratica'];
        $codiceFiscale = $riga['codiceFiscale'];
        $comodity = $riga['comodity'];
        $offerta = $riga['offerta'];
        $dataStipula = date('Y-m-d', strtotime(strtr($riga['dataStipula'], '/', '-')));
        $metodoPagamento = $riga['metodoPagamento'];
        $statoPDA = $riga['statoPDA'];
        $statoPost = $conn19->real_escape_string($riga['statoPost']);
        $noteStato = $conn19->real_escape_string($riga['noteStato']);
        $mandato = $conn19->real_escape_string($riga['mandato']);
        $dataImport = date('Y-m-d', strtotime(strtr($riga['dataImport'], '/', '-')));    
        
        $sincro =[
            "id_contratto_crm"=>$pratica,
          "codice_fiscale"=>$codiceFiscale,
            "fornitore"=>$mandato,
            "data_stipula"=>$dataStipula,
            "offerta"=>$offerta,
            "stato"=>$statoPost,
            "service_type"=>$comodity,
            "metodo_pagamento"=>$metodoPagamento,
            "statoPda"=>$statoPDA,
            "note_stato"=>$noteStato,
            "data_import"=>$dataImport,
        ];

        
        
        
        
    }
}



$json= json_encode($sincro);

$url = "http://0.0.0.0:8000/api/contracts/sync";


// Inizializza cURL
$ch = curl_init($url);

// Configura le opzioni cURL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer Cvt4BMYExu1vrlGSy8wtvTZ464pVl7jESOf6Sre5pUKpQ7xkJceEchC0z7RJuuLD"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

// Esegui la richiesta e ottieni la risposta
$response = curl_exec($ch);

// Controlla se ci sono errori
if (curl_errno($ch)) {
    echo 'Errore cURL: ' . curl_error($ch);
} else {
    echo 'Risposta API: ' . $response;
}

// Chiudi la sessione cURL
curl_close($ch);
