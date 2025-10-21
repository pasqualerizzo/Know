<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
session_start();

require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscallLead.php";

$obj = new connessioneSiscallLead();
$conn = $obj->apriConnessioneSiscallLead();

$cont = 0;
$saltato = 0;
$eseguite = 0;
$oggi = date('Y-m-d');

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
            $numero = $riga[0];
            $gcl = $riga[1];

            $sql = "SELECT lead_id FROM vicidial_list WHERE list_id='2000' and phone_number='$numero'";
            $risultato = $conn->query($sql);
            if ($risultato->num_rows > 0) {
                $r = $risultato->fetch_array();
                $lead = $r[0];
                //$queryinsert = "insert into custom_2000 (lead_id,idCrm) value ('$lead','$gcl')";
                $queryinsert="update custom_2000 set idCrm='$gcl' where lead_id='$lead'";
                $conn->query($queryinsert);
            }
        }
    }
}






fclose($file);
?>

<html>
    <head>
        <meta charset="UTF-8">

        <title>Import Enel</title>
    </head>
    <body>

        <form name="home" action="../pannello.php" method="POST">
            <label for="conteggio">Conteggio</label>
            <input type="text" id="conteggio" name="conteggio" value=<?php echo $cont; ?> readonly style="position:  absolute; left: 200px">
            <br>
            <label for="conteggio">Eseguite</label>
            <input type="text" id="conteggio" name="conteggio" value=<?php echo $eseguite; ?> readonly style="position:  absolute; left: 200px">
            <br>

            <input type="submit" value="Nuovo Import" name="nuovoImport" />
        </form>

    </body>
</html>

