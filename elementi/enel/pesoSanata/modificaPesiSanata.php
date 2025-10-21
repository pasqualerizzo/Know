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

$query = "SELECT * FROM `enelPesiSanata` where id='$id'";
$risultato = $conn19->query($query);
$riga = $risultato->fetch_array();
$conteggio = $risultato->num_rows;
?>

<html>
    <head>
       <head>
        <title>Metrics: Enel</title>
         <link href="../../../css/tabella.css" rel="stylesheet">
        <link href="../../../css/sidebar.css" rel="stylesheet">
       

    </head>
    <body>
        <header>
            <h1 class="titolo">Enel:<br>Mod. P. Sanata</h1>
        </header>
        <div>
            <input type="hidden"  id="permessi" value=<?= $visualizzazione ?>>
        </div>
<?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
        <div class="pagina">
            <form action="queryModificaPesiSanata.php" method="POST" name="modulo">
                <fieldset>
                    <legend>Modifica Peso Sanata</legend>
                    <label for="id">ID</label>
                    <input type="text" name="id" id="id" readonly value=<?= $riga[0] ?>>
                    <br>
                    <label for="data">Data</label>
                    <input type="text" id="data" name="data" readonly value=<?= $riga[1] ?>>
                    <br>
                    <label for="tipoCampagna">Tipo Campagna</label>
                    <input type="text" id="tipoCampagna" name="tipoCampagna" readonly value=<?= $riga[3] ?>>
                    <br>
                    <label for="valore">Valore</label>
                    <input type="text" id="valore" name="valore" readonly value=<?= $riga[4] ?>>
                    <label for="peso">Peso</label>
                    <input type="number" id="peso" name="peso" min="0" max="100" step="0.001"  value=<?= $riga[5] ?>>
                    <br>

                    <label for="idDescrizione">ID Peso</label>
                    <input type="number" value=<?= $riga[6] ?> id="idDescrizione" name="idDescrizione" readonly="true">

                    <label for="descrizione">Descrizione</label>
                    <select id="descrizione" name="descrizione" onchange="descrizioneID(this)">
                        <option value="" <?php
if ($riga[7] == "") {
    echo "selected";
}
?>>-</option>
                        <option value="valore" <?php
if ($riga[7] == "valore") {
    echo "selected";
}
?>>Valore</option>
                        <option value="percentuale" <?php
if ($riga[7] == "percentuale") {
    echo "selected";
}
?>>Percentuale</option>
<?= $riga[7] ?>
                    </select>
                    <br>
                    <input type="submit" value="Aggiorna">
                </fieldset>
            </form>



        </div>
    </body>
    <script>

        function redirigi(sel) {
            var data = sel.options[sel.selectedIndex].value;
            window.location.href = "tabellaPesiSanata.php?meseSelezionato=" + data;

        }




        function descrizioneID(sel) {
            var data = sel.options[sel.selectedIndex].value;
            console.log(data)
            if (data === "valore") {
                document.getElementById("idDescrizione").value = "1";
            } else if (data === "percentuale") {
                document.getElementById("idDescrizione").value = "2";
            } else {
                document.getElementById("idDescrizione").value = "0";
            }

        }

    </script>
</html>


