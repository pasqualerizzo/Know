<?php

require "/Applications/MAMP/htdocs/Know/connessione/connessione_msqli_vici.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrm.php";
require "/Applications/MAMP/htdocs/Know/funzioni/funzioniPlenitude.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$arrayMacroStato = arrayMacroStato($conn19);

foreach ($arrayMacroStato as $key => $value) {
    if (array_key_exists($key, $arrayMacroStato)) {
        
    } else {
        $key . "<br>";
    }
}
?>
