<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
date_default_timezone_set('Europe/Rome');

session_start();

require "/Applications/MAMP/htdocs/Know/siscall/funzioni/funzioni.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscallLead.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$obj = new connessioneSiscallLead();
$connS2 = $obj->apriConnessioneSiscallLead();

$cont = 0;
$elencoOperatori = elencoOperatori($connS2);

// apertura file
$csv = $_FILES['importOreHeracom'];
$nomeFile = $csv['tmp_name'];
$sizeFile = $csv['size'];

$queryL = "INSERT INTO `importOreHeracom`(`operatore`, `data`, `oreDichiarate`, `user`, `level`, `dataAssunzione`, `nomeCompleto`, `sede`) VALUES (?,?,?,?,?,?,?,?)";
$prepareL = $conn19->prepare($queryL);
$prepareL->bind_param('ssisssss', $operatore, $data, $secondi, $user, $livello, $dataAssunzione, $nomeCompleto, $sede,);

if (($file = fopen($nomeFile, "r")) !== false) {
    while (($datiOperatore = fgetcsv($file, $sizeFile, ";")) !== false) {
        if ($cont == 0) {
            $cont++;
        } else {
            $cont++;
            $operatore = mb_strtolower($conn19->real_escape_string($datiOperatore[0]));
            $data = $datiOperatore[1];
            //$dateFile = DateTime::createFromFormat('d/m/Y', $datiOperatore[1]);
            //$data = $dateFile->format('Y-m-d');
            $secondi = $conn19->real_escape_string($datiOperatore[2]);

            $ope = $elencoOperatori[$operatore];

            $user = $ope[0];

            $livello = $ope[1];
            $dataAssunzione = $ope[2];
            $nomeCompleto = $ope[3];
            $sede = $ope[4];

            $prepareL->execute();
        }
    }
}
fclose($file);
$prepareL->close();
$conn19->close();
?>

<html>
    <head>
        <meta charset="UTF-8">

        <title>Import ore Heracom</title>
    </head>
    <body>

        <form name="home" action="index.php" method="POST">
            <label for="conteggio">Conteggio</label>
            <input type="text" id="conteggio" name="conteggio" value=<?php echo $cont - 1; ?> readonly>
            <br>
            
            <input type="submit" value="Nuovo Import" name="nuovoImport" />
        </form>

    </body>
</html>