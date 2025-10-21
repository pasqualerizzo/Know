<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
session_start();

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require_once './invioCrm.php';
// apertura file
$csv = $_FILES['importCSVOperatori'];
$nomeFile = $csv['tmp_name'];
$sizeFile = $csv['size'];
$dataFile=$_POST["dataFile"];

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$cont = 0;
$err = 0;
$saltato = 0;
$vuoto = 0;

$lista = "";
$campagna = "";
$leadId = "INVALID";

if (($file = fopen($nomeFile, "r")) !== false) {
    while (($riga = fgetcsv($file, $sizeFile, ",")) !== false) {
        if ($cont == 0) {
            $cont++;
        } else {
            $cont++;
            $nomeCampagna= $riga[0];
            $importoSpeso=str_replace(["€","$"], "", $riga[9]);
            $click=$riga[5];
            $risultati=$riga[7];
            $impression=$riga[6];
            $costoPerRisultato=str_replace("€", "", $riga[8]);
            
            $costoPerRisultato=($costoPerRisultato=='-')?0:$costoPerRisultato;
            
            $idAccount="INDEED";
            $nomeAccount="Novaholding";
            
            
            $query = "INSERT INTO `costiRecruiting`"
                    . "(`giorno`, `id_account`, `nome_account`, `nome_campagna`, `importo_speso`, `clicks`, `risultati`, `impression`, `costo_per_risultato`, `provenienza`, `gruppo`) "
                    . "VALUES "
                    . "('$dataFile','$idAccount','$nomeAccount','$nomeCampagna','$importoSpeso','$click','$risultati','$impression','$costoPerRisultato','$idAccount','$nomeCampagna')";
            try{
            $conn19->query($query);
            } catch (Exception $e){
                $err++;
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

            <form name="home" action="../index.php" method="POST">
                <label for="conteggio">Conteggio</label>
                <input type="text" id="conteggio" name="conteggio" value=<?php echo $cont - 1; ?> readonly>
                <br>
               
                <label for="errore">Non Importati</label>
                <input type="text" id="errore" name="errore" value=<?php echo $err; ?> readonly>
                <br>
           
                        
            <input type="submit" value="Nuovo Import" name="nuovoImport" />
        </form>

    </body>
</html>



