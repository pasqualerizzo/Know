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
?>

<html>
    <head>
        <title>Aggiunta Campagna SMS</title>
         <link href="../../../css/tabella.css" rel="stylesheet">
        <link href="../../../css/sidebar.css" rel="stylesheet">
   

    </head>
    <body>
        <header>
            <h1 class="titolo">Aggiunta Campagna SMS</h1>
        </header>
        <div>
            <input type="hidden"  id="permessi" value=<?= $visualizzazione ?>>
        </div>
<?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
        <div class="pagina">
            <form action="queryAggiuntaCampagna.php" method="POST">
                <fieldset>
                    <legend>Aggiunta Campagna Marketing</legend>
                    
                    <label for="nomeCampagna">Nome Campagna</label>
                    <input type="text" name="nomeCampagna"  >
                    <br>
                    
                    <label for="pezzi">Pezzi
                        <input type="number" name="pezzi" step="1">
                    </label>
                    <label for="costo">Costo</label>
                    <input type="number" name="costo"   min="0" max="50000" step="0.01" >
                    <br>
                    <label for="dataInserimento">Data Inserimento</label>
                    <input type="date" name="dataInserimento"   >
                    <br>
                    
                    <input type="submit" value="Inserisci">
                    <input type="submit" formaction="../index.php" value="Indietro">
                </fieldset>
            </form>



        </div>
    </body>
    <script>

    </script>
</html>


