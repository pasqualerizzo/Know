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

$query = "SELECT * FROM `tabellaCosti` where id='$id'";
$risultato = $conn19->query($query);
$riga = $risultato->fetch_array();
$conteggio = $risultato->num_rows;
$mese = $riga['mese'];

$sede = $riga["sede"];
$costiStruttura = $riga["costiStruttura"];
$costiIndiretti = $riga["costiIndiretti"];

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
            <h1 class="titolo">Modifica Riga Tabella Costi</h1>
        </header>
        <div>
            <input type="hidden"  id="permessi" value=<?= $visualizzazione ?>>
        </div>
<?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
        <div class="pagina">
            <form action="queryModificaTabellaCosti.php" method="POST" >
                <fieldset >
                    <legend>Aggiungi Tabella Costi</legend>
<label for="id">id</label>
                    <input type="text" name="id"  value="<?= $id ?>" >
                    <label for="sede">Sede</label>
                    <input type="text" name="sede" required value="<?= $sede ?>" >
                    <br>
                    <label for="mese">Mese
                        <input type="text" name="mese" value="<?= $mese ?>">
                    </label>
                    <br>
                    <label for="costoFissi">Costi Struttura</label>
                    <input type="number" name="costiStruttura" step="0.01" value="<?= $costiStruttura ?>" >
                    
                    <label for="costoVariabili">Costi Indiretti</label>
                    <input type ="number" name="costiIndiretti"  step="0.01" value="<?= $costiIndiretti ?>">
                    <br>

                    <input type="submit" value="Modifica">
                </fieldset>
            </form>



        </div>
    </body>
    <script>

    </script>
</html>


