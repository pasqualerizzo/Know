<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
$gruppoTL = filter_input(INPUT_POST, 'gruppotl');
$tipo=filter_input(INPUT_POST, 'tipo');
$ore=filter_input(INPUT_POST, 'ore');
$plenitudePdp=filter_input(INPUT_POST, 'plenitudePdp');
$irenPdp=filter_input(INPUT_POST, 'irenPdp');
$enelPdp=filter_input(INPUT_POST, 'enelPdp');

$vivigasPdp=filter_input(INPUT_POST, 'vivigasPdp');
$polizzePdp=filter_input(INPUT_POST, 'polizzePdp');
$enelInPdp=filter_input(INPUT_POST, 'enelInPdp');
$timPdp=filter_input(INPUT_POST, 'timPdp');
$heracomPdp=filter_input(INPUT_POST, 'heracomPdp');
$sede= filter_input(INPUT_POST, 'sede');



require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$query = "UPDATE `obbiettivoTL` SET "
        . " gruppoTL='$gruppoTL',tipo='$tipo',ore='$ore',"
        . " plenitudePdp='$plenitudePdp',irenPdp='$irenPdp',enelPdp='$enelPdp', "
        . " vivigasPdp='$vivigasPdp', polizzePdp='$polizzePdp', enelInPdp='$enelInPdp', timPdp='$timPdp', Heracom='$heracomPdp',sede='$sede'"
        . " WHERE id='$id'";
$conn19->query($query);

$obj19->chiudiConnessione();
header("location:index.php");

?>