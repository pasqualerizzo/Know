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
$data = date("Y-m-01");

if ($logged == false) {
    header("location:https://ssl.novadirect.it/Know/index.php?errore=logged");
}
?>

<html>
    <head>
        <title>Tabella Costi</title>
        <link href="../../css/tabella.css" rel="stylesheet">
        <link href="../../css/sidebar.css" rel="stylesheet">


    </head>
    <body>
        <header>
            <h1 class="titolo">Aggiungi Riga Tabella Costi</h1>
        </header>
        <div>
            <input type="hidden"  id="permessi" value=<?= $visualizzazione ?>>
        </div>
        <?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
        <div class="pagina">
            <form action="queryAggiuntaTabellaCosti.php" method="POST" >
                <fieldset >
                    <legend>Aggiungi Tabella Costi</legend>

                    <label for="sede">Sede</label>
                    <input type="text" name="sede" required >
                    <br>
                    <label for="mese">Mese
                        <input type="text" name="mese" value="<?= $data ?>">
                    </label>
                    <br>
                    <label for="costoFissi">Costi Struttura</label>
                    <input type="number" name="costiStruttura" step="0.01"  >
                    
                    <label for="costoVariabili">Costi Indiretti</label>
                    <input type ="number" name="costiIndiretti"  step="0.01" >
                    <br>

                    <input type="submit" value="Inserisci">
                </fieldset>
            </form>



        </div>
    </body>
    <script>

    </script>
</html>


