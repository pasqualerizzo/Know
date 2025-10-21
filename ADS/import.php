<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);


//echo "start";

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$cont = 0;
$modificato = 0;
$ModificatoStao = 0;

$data = date('d/m/Y');

// apertura file
$csv = $_FILES['import'];
$nomeFile = $csv['tmp_name'];
$sizeFile = $csv['size'];

if (($file = fopen($nomeFile, "r")) !== false) {
    while (($riga = fgetcsv($file, $sizeFile, ";")) !== false) {
        if ($cont == 0) {
            $cont++;
        } else {
            $cont++;
            $nome = $riga[0];
            $importo = str_replace(",", ".", $riga[1]);
            $dataInizio =  date("Y-m-d", strtotime($riga[2]));
            $dataFine = $riga[3];
            $account = $riga[4];

            $queryStato = "INSERT INTO"
                    . " `facebook`"
                    . "(`giorno`, `id_account`, `nome_account`, `nome_campagna`, `importo_speso`, `risultati`, `impression`, `costo_per_risultato`)"
                    . " VALUES"
                    . " ('$dataInizio','$account','Risparmiami','$nome','$importo',0,0,0.0)";
            echo $queryStato."<br>";
            $risultato = $conn19->query($queryStato);
        }
    }
}



fclose($file);
?>

