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
        <title>Tabella Costi Messaggi</title>
        <link href="../../css/tabella.css" rel="stylesheet">
        <link href="../../css/sidebar.css" rel="stylesheet">


    </head>
    <body>
        <header>
            <h1 class="titolo">Aggiungi Invio Messaggi</h1>
        </header>
        <div>
            <input type="hidden"  id="permessi" value=<?= $visualizzazione ?>>
        </div>
        <?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
        <div class="pagina">
            <form action="queryAggiuntaTabellaCosti.php" method="POST" >
                <fieldset >
                    <legend>Aggiungi Invio Messaggi</legend>

                    <label for="sede">UTM</label>
                    <input type="text" name="utm" required >
                    <br>
                    <label for="dataImport">Data Invio Messaggi
                        <input type="date" name="dataImport" required>
                    </label>
                    <br>
                    <label for="costoVariabile">Costo Varibili Invio Messaggi</label>
                    <input type="number" name="costoVariabile" step="0.01"  >
                    
                    <label for="costoFisso">Costo Fisso Invio Messaggi</label>
                    <input type ="number" name="costoFisso"  step="0.01" >
                    <br>
                    
                    <label for="pezzi">Messaggi Inviati</label>
                    <input type ="number" name="pezzi"  step="1" >
                    <br>
                    
                    

                    <input type="submit" value="Inserisci">
                </fieldset>
            </form>



        </div>
    </body>
    <script>

    </script>
</html>


