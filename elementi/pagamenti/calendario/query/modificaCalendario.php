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

$query = "SELECT * FROM `calendario` where id='$id'";
$risultato = $conn19->query($query);
$riga = $risultato->fetch_array();
$conteggio = $risultato->num_rows;
?>

<html>
    <head>
        <title>Pagamento: Gara Ore</title>
         <link href="../../../../css/tabella.css" rel="stylesheet">
        <link href="../../../../css/sidebar.css" rel="stylesheet">
    </head>
    <body>
        <header>
            <h1 class="titolo">Pagamento:<br>Gara Ore</h1>
        </header>
        <div>
            <input type="hidden"  id="permessi" value=<?= $visualizzazione ?>>
        </div>
<?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
        <div class="pagina">
            <form action="query/queryModificaGaraOre.php" method="POST">
                <fieldset>
                    <legend>Modifica Gara Ore</legend>
                    <label for="id">ID</label>
                    <input type="text" name="id" readonly value=<?=$riga[0]?>>
                    <br>
                    <label for="mese">mese</label>
                    <input type="text" name="mese" readonly value=<?=$riga[3]?>>
                    
                    <label for="fascia">giorno</label>
                    <input type="text" name="giorno" readonly value=<?=$riga[1]?>>
                    <br>
                    <label for="valore">Valore</label>
                    <input type="number" min="0" max="2" step="0.1" name="valore"  value=<?=$riga[2]?>>
                    
                    <br>
                    <input type="submit" value="Aggiorna">
                    
                </fieldset>
            </form>
        </div>
    </body>
    <script>

    </script>
</html>


