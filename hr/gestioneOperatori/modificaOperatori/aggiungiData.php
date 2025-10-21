<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
session_start();

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

/**
 * variabili
 */
$nomeCompleto = filter_input(INPUT_POST, "nome_completo");
$dataCessazione = filter_input(INPUT_POST, "dataCessazione");
$dataCessazioneVici = date('Y-m-d', strtotime($dataCessazione . " + 2 months"));

$err = 0;
$saltato = 0;

$queryRicerca = ""
        . "Select * "
        . " From gestioneOperatori "
        . " Where nomeCompleto='$nomeCompleto'";
$risultato = $conn19->query($queryRicerca);
if ($risultato->num_rows == 0) {
    $queryInserimento = ""
            . " INSERT INTO "
            . " `gestioneOperatori` "
            . " (`nomeCompleto`, `dataCessazione`, `dataCessazioneVici`) "
            . " VALUES "
            . " ('$nomeCompleto','$dataCessazione','$dataCessazioneVici')";
    try {
        $conn19->query($queryInserimento);
    } catch (Exception $ex) {
        $err++;
    }
} else {
    $saltato++;
}
?>

    <html>
        <head>
            <meta charset="UTF-8">

            <title>Import Operatore</title>
        </head>
        <body>

            <form name="home" action="../creaTabellaOperatore.php" method="POST">
                
               
                <label for="errore">Non Importati</label>
                <input type="text" id="errore" name="errore" value=<?php echo $err; ?> readonly>
                <br>
                
                <label for="saltati">Saltati</label>
                <input type="text" id="salati" name="saltati" value=<?php echo $saltato; ?> readonly>
                <br>
                        
            <input type="submit" value="Nuovo Import" name="nuovoImport" />
        </form>

    </body>
</html>
 