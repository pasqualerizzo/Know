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

$query = "SELECT * FROM `greenPesiInvio` where id='$id'";
$risultato = $conn19->query($query);
$riga = $risultato->fetch_array();
$conteggio = $risultato->num_rows;
?>

<html>
    <head>
        <head>
        <title>MagePunti: Vivigas Group</title>
         <link href="../../css/tabella.css" rel="stylesheet">
        <link href="../../css/sidebar.css" rel="stylesheet">
        <script type='text/javascript' src='../js/jquery.min.js'></script>
        <script type='text/javascript' src='../js/script.js'></script>

    </head>
    <body>
        <header>
            <h1 class="titolo">Green Network:<br>Mod P. Invio</h1>
        </header>
        <div>
            <input type="hidden"  id="permessi" value=<?= $visualizzazione ?>>
        </div>
<?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
        <div class="pagina">
            <form action="queryModificaPesiInvio.php" method="POST">
                <fieldset>
                    <legend>Modifica Peso Comodity</legend>
                    <label for="id">ID</label>
                    <input type="text" name="id" readonly value=<?=$riga[0]?>>
                    <br>
                    <label for="data">Data</label>
                    <input type="text" name="data" readonly value=<?=$riga[1]?>>
                    <br>
                    <label for="tipoCampagna">Tipo Campagna</label>
                    <input type="text" name="tipoCampagna" readonly value=<?=$riga[3]?>>
                    <br>
                    <label for="valore">Valore</label>
                    <input type="text" name="valore" readonly value=<?=$riga[4]?>>
                    <label for="peso">Peso</label>
                    <input type="number" name="peso" min="0" max="5" step="0.001"  value=<?=$riga[5]?>>
                    <br>
                    <input type="submit" value="Aggiorna">
                </fieldset>
            </form>



        </div>
    </body>
    <script>

    </script>
</html>


