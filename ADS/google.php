<?php

header('Access-Control-Allow-Origin: *');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

date_default_timezone_set('Europe/Rome');

error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$dataImport = date("Y-m-d H:i:s");

$obj = new Connessione();
$conn = $obj->apriConnessione();

//
$id_account = $conn->real_escape_string(filter_input(INPUT_POST, "id_account"));

$nome_account = str_replace('_GADS', '', filter_input(INPUT_POST, "nome_account"));

$nome_campagna = $conn->real_escape_string(filter_input(INPUT_POST,"nome_campagna"));

$importo_speso = filter_input(INPUT_POST, "importo_speso");

$importo_speso = str_replace(",","", $importo_speso);

$risultati =  $conn->real_escape_string(filter_input(INPUT_POST,"risultati"));

$impression = $conn->real_escape_string(filter_input(INPUT_POST, "impression"));

$costo_per_risultato =$conn->real_escape_string(filter_input(INPUT_POST, "costo_per_risultato"));

$giorno = date("Y-m-d", strtotime(str_replace('/', '-', filter_input(INPUT_POST, "giorno"))));

$clicks =filter_input(INPUT_POST, "clicks");

switch ($nome_campagna) {
    case "Search Solo Call Energia - Lazio":
    case "Search Solo Call Energia - Toscana":
    case "Search Solo Call Energia - Veneto":
    case "Search Solo Call Energia - Lombardia":
    case "Search Solo Call Energia - Emilia Romagna":
        $gruppo = "Search Solo Call Reg";
        break;
    case "Search_Call_Generale_10_06_2024":
        $gruppo = "Search_Call_Generale_10_06_2024";
        break;
    default:
        $gruppo = $nome_campagna;
        break;
}


$queryStato = "INSERT INTO"
        . " `facebook`"
        . "(`giorno`, `id_account`, `nome_account`, `nome_campagna`, `importo_speso`, `risultati`, `impression`, `costo_per_risultato`,provenienza,gruppo,clicks)"
        . " VALUES"
        . " ('$giorno','$id_account','$nome_account','$nome_campagna','$importo_speso','$risultati','$impression','$costo_per_risultato','Google ADS','$gruppo','$clicks')";
try {
    $risultato = $conn19->query($queryStato);
} catch (exception $e) {
    echo "ex: " . $e;
    die();
}
echo "ok import effettuato";
