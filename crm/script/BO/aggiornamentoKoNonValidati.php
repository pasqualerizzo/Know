<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrmNuovo.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscallLead.php";
require '/Applications/MAMP/htdocs/Know/funzioni/funzioniSiscall.php';

$objS = new connessioneSiscallLead();
$connS = $objS->apriConnessioneSiscallLead();

$obj = new ConnessioneCrmNuovo();
$connCrm = $obj->apriConnessioneCrmNuovo();

/**
 * Finestra di verifica 1 mesi
 */
$oggi = date('Y-m-d', strtotime("-7 days"));
$dataMinore = date('Y-m-d', strtotime("-3 months"));

$contrattiInseriti = [];

$motivazioneKo = "('KO NON VALIDATO')";
$lista = " (501,502,503,504,505)";
$listaImport = 505;
$queryRicerca = "SELECT "
    . "plenicf.nome AS 'nome', "
    . "plenicf.cognome AS 'cognome', "
    . "plenicf.cellulareprimario AS 'cellulare', "
    . "plenicf.codicefiscale AS 'codiceFiscale', "
    . "plenicf.noteplicoluce AS 'noteStatoLuce', "
    . "plenicf.noteplicogas AS 'noteStatoGas', "
    . " plenicf.datasottoscrizionecontratto AS 'dataContratto',"
    . " plenicf.pod AS 'pod', "
    . " plenicf.codicepdr AS 'codicepdr', "
    . " plenicf.iban AS 'iban',"
    ." CONCAT(plenicf.indirizzofornitura,' ',plenicf.comunefornitura,' ',plenicf.provinciafornitura) AS 'indirizzo', "
    ." plenicf.tipoacquisizione AS 'tipoacquisizione', "
    ."plenicf.notebackoffice AS 'notebackoffice' "
    . "FROM "
    . "vtiger_plenitude as plenicf "
    . "WHERE "
    . " plenicf.datasottoscrizionecontratto between '$dataMinore' and '$oggi' "
    . " AND plenicf.statopda in $motivazioneKo";

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

        $pod = $riga['pod'];
        $codicepdr = $riga['codicepdr'];
        $iban = $riga['iban'];
        $indirizzo = $riga['indirizzo'];
        $tipoacquisizione = $riga['tipoacquisizione'];
        $noteBackOffice = $riga['notebackoffice'];

        $query = "SELECT * FROM vicidial_list WHERE phone_number='$telefono' and list_id in $lista";
        $ris = $connS->query($query);
        if ($ris->num_rows > 0) {

        } else {
            inserisciLeadKoNonValidati($listaImport, $telefono, $nome, $cognome, $noteStatoLuce, $noteStatoGas, $codiceFiscale, $dataContratto,$pod,$codicepdr,$iban,$indirizzo,$tipoacquisizione,$noteBackOffice);
        }
    }
}

$obj->chiudiConnessioneCrm();
$objS->chiudiConnessioneSiscallLead();

?>
