<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();


$username=filter_input(INPUT_POST, "username");
$password=filter_input(INPUT_POST, "password");
$livello= filter_input(INPUT_POST, "livello");

$password_hash = password_hash($password, PASSWORD_BCRYPT);

$queryInserimento="INSERT INTO `login`( `username`, `password`, `livello`) VALUES ('$username','$password_hash','$livello')";
$risultato=$conn19->query($queryInserimento);

