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
$id = filter_input(INPUT_GET, "id");

$query = "SELECT * FROM `campagnaMarketing` where id='$id'";
$risultato = $conn19->query($query);
$conteggio = $risultato->num_rows;
if ($conteggio == 0) {
    
} else {
    $riga = $risultato->fetch_array();
    $nomeCampagna = $riga['nomeCampagna'];
    $pezzi = $riga['pezzi'];
    $costo = $riga['costo'];
    $dataInserimento = $riga['dataInserimento'];
}
?>

<html>
    <head>
        <title>Modifica Campagna SMS</title>
         <link href="../../../css/tabella.css" rel="stylesheet">
        <link href="../../../css/sidebar.css" rel="stylesheet">
   

    </head>
    <body>
        <header>
            <h1 class="titolo">Modifica Campagna SMS</h1>
        </header>
        <div>
            <input type="hidden"  id="permessi" value=<?= $visualizzazione ?>>
        </div>
<?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
        <div class="pagina">
            <form action="queryModificaCampagna.php" method="POST">
                <fieldset>
                    <legend>Modifica Campagna Marketing</legend>
                    <input type="hidden" name="id" value=<?=$id?>>
                    
                    <label for="nomeCampagna">Nome Campagna</label>
                    <input type="text" name="nomeCampagna" value=<?=$nomeCampagna?> >
                    <br>
                    
                    <label for="pezzi">Pezzi
                        <input type="number" name="pezzi" step="1" value=<?=$pezzi?>
                    </label>
                    <label for="costo">Costo</label>
                    <input type="number" name="costo"   min="0" max="50000" step="0.01" value=<?=$costo?> >
                    <br>
                    <label for="dataInserimento">Data Inserimento</label>
                    <input type="date" name="dataInserimento"  value=<?=$dataInserimento?> >
                    <br>
                    
                    <input type="submit" value="Modifica">
                    <input type="submit" formaction="../index.php" value="Indietro">
                </fieldset>
            </form>



        </div>
    </body>
    <script>

    </script>
</html>


