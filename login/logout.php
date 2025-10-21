<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();
foreach ($_SESSION as $chiave => $valore) {
    unset($_SESSION[$chiave]);
}
session_destroy();
header("location:../index.php");

