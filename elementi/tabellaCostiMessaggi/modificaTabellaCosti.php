<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

$logged = $_SESSION["login"];
$livello = $_SESSION["livello"];
$user = $_SESSION["username"];
$ip = $_SESSION["ip"];
$visualizzazione = $_SESSION["visualizzazione"];
$sede = $_SESSION["sede"];
$permessi = $_SESSION["permessi"];

if ($logged == false) {
    header("location:https://ssl.novadirect.it/Know/index.php?errore=logged");
}

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$id = $_GET["id"];

$query = "SELECT * FROM `tabellaCostiMessaggi` where id='$id'";
$risultato = $conn19->query($query);
$riga = $risultato->fetch_array();
$conteggio = $risultato->num_rows;
$dataImport = $riga['dataImport'];

$utm = $riga["UTM"];
$pezzi=$riga["pezzi"];

$costoVariabile = $riga["costoVariabile"];
$costiFisso = $riga["costiFisso"];
$id=$riga["id"];

$obj19->chiudiConnessione();
?>

<html>
    <head>
        <title>Tabella Costi</title>
        <link href="../../css/tabella.css" rel="stylesheet">
        <link href="../../css/sidebar.css" rel="stylesheet">


    </head>
    <body>
        <header>
            <h1 class="titolo">Modifica Riga Messaggi</h1>
        </header>
        <div>
            <input type="hidden"  id="permessi" value=<?= $visualizzazione ?>>
        </div>
<?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
        <div class="pagina">
            <form action="queryModificaTabellaCosti.php" method="POST" >
                <fieldset >
                    <legend>Modifica Riga Messaggio</legend>
<label for="id">id</label>
                    <input type="text" name="id"  value="<?= $id ?>" >
                    <label for="utm">UTM</label>
                    <input type="text" name="utm" required value="<?= $utm ?>" >
                    <br>
                    <label for="dataImport">Data Invio
                        <input type="date" name="dataImport" value="<?= $dataImport ?>">
                    </label>
                    <br>
                    <label for="costoVariabile">Costi Variabile</label>
                    <input type="number" name="costoVariabile" step="0.01" value="<?= $costoVariabile ?>" >
                    
                    <label for="costiFisso">Costi Fisso</label>
                    <input type ="number" name="costiFisso"  step="0.01" value="<?= $costiFisso ?>">
                    <br>

                    <label for="pezzi">Messaggi Inviati</label>
                    <input type ="number" name="pezzi"  step="1" value="<?=$pezzi ?>">
                    <br>
                    
                    <input type="submit" value="Modifica">
                </fieldset>
            </form>



        </div>
    </body>
    <script>

    </script>
</html>


