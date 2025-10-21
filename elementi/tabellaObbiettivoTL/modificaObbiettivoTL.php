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

$query = "SELECT * FROM `obbiettivoTL` where id='$id'";
$risultato = $conn19->query($query);
$riga = $risultato->fetch_array();
$conteggio = $risultato->num_rows;
$obj19->chiudiConnessione();
?>

<html>
    <head>
        <title>MagePunti: Union Group</title>
        <link href="../../css/tabella.css" rel="stylesheet">
        <link href="../../css/sidebar.css" rel="stylesheet">


    </head>
    <body>
        <header>
            <h1 class="titolo">Modifica Obbiettivo TL</h1>
        </header>
        <div>
            <input type="hidden"  id="permessi" value=<?= $visualizzazione ?>>
        </div>
        <?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
        <div class="pagina">
            <form action="queryModificaObbiettivoTL.php" method="POST">
                <fieldset style="display:flex;flex-direction: column;flex-wrap: nowrap;align-items: stretch;">
                    <legend>Modifica Obbiettivo TL</legend>
                    <label for="id">ID</label>
                    <input type="text" name="id" readonly value=<?= $riga['id'] ?>>
                    <br>
                    <div>
                    <label for="gruppotl">Gruppo TL</label>
                    <input type="text" name="gruppotl" id="gruppotl"  value=<?= $riga['gruppoTL'] ?>>
                    <label for="tipo">tipo</label>
                    <input type="text" name="tipo" value=<?= $riga['tipo'] ?>>
                    <label for="sede">Sede</label>
                    <input type="text" name="sede" value=<?= $riga['sede'] ?>>
                    <label for="mese">Mese
                        <input type="text" name="mese" readonly value=<?= $riga['mese'] ?>>
                    </label>
                    </div>

                    
                    <br>
                    <label for="ore">Ore</label>
                        <input tipe="number" name="ore" id="ore" value=<?= $riga['ore'] ?>>
                    <br>
                    <label for="plenitudePdp">Plenitude PDP</label>
                        <input tipe="number" name="plenitudePdp" id="plenitudePdp" value=<?= $riga['plenitudePdp'] ?>>
                    <br>
                          <label for="irenPdp">Iren PDP</label>
                        <input tipe="number" name="irenPdp" id="irenPdp" value=<?= $riga['irenPdp'] ?>>
                    <br>
                    <label for="enelPdp">Enel PDP</label>
                        <input tipe="number" name="enelPdp" id="enelPdp" value=<?= $riga['enelPdp'] ?>>
                    <br>
                    <label for="vivigasPdp">Vivigas PDP</label>
                        <input tipe="number" name="vivigasPdp" id="vivigasPdp" value=<?= $riga['vivigasPdp'] ?>>
                    <br>
                    <br>
                    <label for="enelInPdp">EnelIn PDP</label>
                        <input tipe="number" name="enelInPdp" id="enelInPdp" value=<?= $riga['enelInPdp'] ?>>
                    <br>
                    <br>
                    <label for="timPdp">Tim PDP</label>
                        <input tipe="number" name="timPdp" id="timPdp" value=<?= $riga['enelInPdp'] ?>>
                    <br>
                    <br>
                    <label for="heracomPdp">Heracom</label>
                        <input tipe="number" name="heracomPdp" id="heracomPdp" value=<?= $riga['enelInPdp'] ?>>
                    <br>
                    <label for="polizzePdp">Polizze PDP</label>
                        <input tipe="number" name="polizzePdp" id="polizzePdp" value=<?= $riga['polizzePdp'] ?>>
                    <br>
                    <input type="submit" value="Aggiorna">
                </fieldset>
            </form>



        </div>
    </body>
    <script>

    </script>
</html>


