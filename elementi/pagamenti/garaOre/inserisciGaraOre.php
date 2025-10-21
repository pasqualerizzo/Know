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

//$id = $_GET["id"];
//
//$query = "SELECT * FROM `garaOre` where id='$id'";
//$risultato = $conn19->query($query);
//$riga = $risultato->fetch_array();
//$conteggio = $risultato->num_rows;
?>

<html>
    <head>
        <title>Pagamento: Gara Ore</title>
         <link href="../../../css/tabella.css" rel="stylesheet">
        <link href="../../../css/sidebar.css" rel="stylesheet">
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
            <form action="query/queryInsersciRigaGaraOre.php" method="POST">
                <fieldset>
                    <legend>Modifica Gara Ore</legend>
                    
                    <label for="nome">Nome</label>
                    <input type="text" name="nome" >
                    
                    <label for="fascia">Fascia</label>
                    <input type="text" name="fascia" >
                    <br>
                    <label for="data">Mese</label>
                    <input type="date" name="data" >
                    <br>
                    <label for="pezziMinimi">Pezzi Minimi
                        <input type="number"  min="0" max="1000" step="0.1" name="pezziMinimi"  >
                    </label>
                    <label for="pezziMassimi">Pezzi Massimi
                        <input type="number"  min="0" max="1000" step="0.1" name="pezziMassimi"  >
                    </label>
                    <br>
                    <label for="oreMinimi">Ore Minime
                        <input type="number"  min="0" max="1000" step="1" name="oreMinime"  >
                    </label>
                    <br>
                      <label for="oreAutorizzate">Ore Autorizzate
                        <input type="number"  min="0" max="1000" step="1" name="oreAutorizzate"  >
                    </label>
                    <br>
                    <label for="valore">Valore</label>
                    <input type="number" name="valore" min="0" max="1000" step="0.1"  >
                    <br>
                    <input type="submit" value="Inserisci">
                    
                </fieldset>
            </form>
        </div>
    </body>
    <script>

    </script>
</html>


