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

$query = "SELECT * FROM `garaPunti` where id='$id'";
$risultato = $conn19->query($query);
$riga = $risultato->fetch_array();
$conteggio = $risultato->num_rows;
?>

<html>
    <head>
        <title>Pagamento: Gara Punti</title>
         <link href="../../../css/tabella.css" rel="stylesheet">
        <link href="../../../css/sidebar.css" rel="stylesheet">


    </head>
    <body>
        <header>
            <h1 class="titolo">Pagamento:<br>Modifica Gara Punti</h1>
        </header>
        <div>
            <input type="hidden"  id="permessi" value=<?= $visualizzazione ?>>
        </div>
<?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
        <div class="pagina">
            <form action="query/queryModificaGaraPunti.php" method="POST">
                <fieldset>
                    <legend>Modifica Gara Punti</legend>
                    <label for="id">ID</label>
                    <input type="text" name="id" readonly value=<?= $riga[0] ?> >
                    <br>
                    <label for="fascia">Fascia</label>
                    <input type="text" name="fascia" readonly value=<?= $riga[1] ?>>
                    <br>
                    <label for="mese">Mese
                        <input type="text" name="mese" readonly value=<?= $riga[2] ?>>
                    </label>
                    <br>
                    <label for="puntiMinimi">Punti Minimi</label>
                    <input type="number" name="puntiMinimi" min="0" max="100" step="0.1" value=<?= $riga[3] ?>>
                    
                    <label for="puntiMassimi">Punti Massimi</label>
                    <input type="number" name="puntiMassimi" min="0" max="1000" step="0.1"  value=<?= $riga[4] ?>> 
                    <br> 
                    <label for="valore">Valore</label>
                    <input type="number" name="valore" min="0" max="100" step="0.1"   value=<?= $riga[5] ?>>
                    
                    <br>
                    <input type="submit" value="Aggiorna">
                    <input type="submit" value='Cancella' style="background-color: red;color:white" formaction='query/queryDeleteRigaGaraOre.php'>
                </fieldset>
            </form>


        </div>
    </body>
    <script>

    </script>
</html>


