<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscall2.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneGt.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscallLead.php";
require "/Applications/MAMP/htdocs/Know/funzioni/funzioniCruscottoKpi.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$obj = new connessioneSiscall2();
$conn = $obj->apriConnessioneSiscall2();

$objGt = new connessioneGt();
$connGt = $objGt->apriConnessioneGt();

$objL = new connessioneSiscallLead();
$connL = $objL->apriConnessioneSiscallLead();


$meseRiferimento = date('Y-m-01', strtotime(filter_input(INPUT_POST, "meseRiferimento")));

$primoLunedi=date("Y-m-d",strtotime("first monday of " . $meseRiferimento));

echo $primoLunedi;




$conn19->close();
?>
