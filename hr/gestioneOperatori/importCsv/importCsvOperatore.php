<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
session_start();

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
// apertura file
$csv = $_FILES['importCSVOperatori'];
$nomeFile = $csv['tmp_name'];
$sizeFile = $csv['size'];

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$cont = 0;
$err = 0;
$saltato = 0;
$vuoto=0;

if (($file = fopen($nomeFile, "r")) !== false) {
    while (($riga = fgetcsv($file, $sizeFile, ";")) !== false) {
        if ($cont == 0) {
            $cont++;
        } else {
            $cont++;
            $nomeCompleto = $riga[0];
            $data = $riga[1];
            if ($data == "") {
                $vuoto++;
            } else {
                list($giorno, $mese, $anno) = explode('/', $data);
                $dataInglese = sprintf('%04d-%02d-%02d', $anno, $mese, $giorno);
                $dataCessazione = date('Y-m-d', strtotime($dataInglese));
                $dataCessazioneVici = date('Y-m-d', strtotime($dataInglese . " + 2 months"));
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
            }
        }
    }
}
?>

    <html>
        <head>
            <meta charset="UTF-8">

            <title>Import Operatore</title>
        </head>
        <body>

            <form name="home" action="../creaTabellaOperatore.php" method="POST">
                <label for="conteggio">Conteggio</label>
                <input type="text" id="conteggio" name="conteggio" value=<?php echo $cont - 1; ?> readonly>
                <br>
               
                <label for="errore">Non Importati</label>
                <input type="text" id="errore" name="errore" value=<?php echo $err; ?> readonly>
                <br>
                <label for="vuoto">Vuoto</label>
                <input type="text" id="vuoto" name="vuoto" value=<?php echo $vuoto; ?> readonly>
                <br>
                
                
                <label for="saltati">Saltati</label>
                <input type="text" id="salati" name="saltati" value=<?php echo $saltato; ?> readonly>
                <br>
                        
            <input type="submit" value="Nuovo Import" name="nuovoImport" />
        </form>

    </body>
</html>