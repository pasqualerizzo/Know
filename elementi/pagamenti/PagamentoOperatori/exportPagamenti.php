<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$meseSelezionato = $_GET["meseSelezionato"];

$listaEsclusi = "("
        . "'',"
        . "'BO',"
        . "'TL',"
        . "'-',"
        . "'RU'"
        . ")";

$query = "SELECT "
        . " nomeCompleto AS 'Operatore',"
        . " mese,"
        . " livello,"
        . " sede,"
        . " if(round((numero)+(orePolizze)-formazione,2)<=0,0,round((numero)+(orePolizze)-formazione,2)) as 'Ore Fatte', "
        . " round(orePagabili,2) as 'Ore Pagabili',"
        . " oreAutorizzate as 'Ore Autorizzate',"
        . " round(vodafonePesoPagato+vivigasPesoPagato+plenitudePesoPagato+greenPesoPagato+enelOutPesoPagato+irenPesoPagato+unionPesoPagato+enelPesoPagato+plenitudePolizzePesoPagato,2) as 'Peso Pagato',"
        . " round(vodafonePesoFormazione+vivigasPesoFormazione+plenitudePesoFormazione+greenPesoFormazione+enelOutPesoFormazione+irenPesoFormazione+unionPesoFormazione+enelPesoFormazione+plenitudePolizzePesoFormazione,2) as 'Peso Formazione',"
        . " puntiPagabili as 'Punti Pagabili',"
        . " valoreOre as 'Valore Ore',"
        . " costoOre as 'Costo Ore',"
        . " valorePezzi as 'Valore Pezzi',"
        . " costoPezzi as 'Costo Pezzi',"
        . " costoTotale as 'Costo Totale',"
        . " giorniLavorati as 'Giorni Lavorabili Mese',"
        . " round(costoGiorni,2) as 'Costo Giornaliero Risorsa',"
        . " round(costoAzienda,2) as 'Costo Azienda(1.4)'"
        . " FROM "
        . " pagamentoMese "
        . " inner join `aggiuntaPagamento` on pagamentoMese.id=aggiuntaPagamento.id "
        . " where "
        . " dataRiferimento='$meseSelezionato'"
        . " and sede not in $listaEsclusi ";
//echo $query;
$risultato = $conn19->query($query);
$conteggio = $risultato->num_rows;
$intestazione = $risultato->fetch_fields();
$el = 0;

$directory = "../elementi/";
$file = "/Applications/MAMP/htdocs/Know/elementi/file.csv";
$intestazioneChiamate = "";

foreach ($intestazione as $info) {
    $intestazioneChiamate .= $info->name;
    $intestazioneChiamate .= ";";
    $el++;
}
$intestazioneChiamate .= "\n";
while ($lista = $risultato->fetch_array()) {
    for ($i = 0;
            $i < 18;
            $i++) {
        if (is_numeric($lista[$i])) {
            $intestazioneChiamate .= str_replace(".", ",", $lista[$i]);
            $intestazioneChiamate .= ";";
        } else {
            $intestazioneChiamate .= $lista[$i];
            $intestazioneChiamate .= ";";
        }        
    }
    $intestazioneChiamate .= "\n";
}





    file_put_contents($file, $intestazioneChiamate);
    header('Content-Description: File Transfer');
    header('Content-type: application/octet-stream');
    header('Content-Transfer-Encoding: binary');
    header("Content-Type: " . mime_content_type($file));
    header("Content-type: text/csv");
    header('Content-Disposition: attachment; filename=expoPagamenti.csv');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    flush();
    readfile($file);

    