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

$query = "SELECT * FROM `plenitudeStatoLuce` where id='$id'";
$risultato = $conn19->query($query);
$riga = $risultato->fetch_array();

$conteggio = $risultato->num_rows;
?>

<html>
    <head>
        <title>MagePunti: Vivigas Group</title>
         <link href="../../css/tabella.css" rel="stylesheet">
        <link href="../../css/sidebar.css" rel="stylesheet">
        <script type='text/javascript' src='../js/jquery.min.js'></script>
        <script type='text/javascript' src='../js/script.js'></script>

    </head>
    <body>
        <header>
            <h1 class="titolo">Plenitude:<br>Mod Stato Luce</h1>
        </header>
        <div>
            <input type="hidden"  id="permessi" value=<?= $visualizzazione ?>>
        </div>
        <?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
        <div class="pagina">
            <form action="queryModificaStatoLuce.php" method="POST">
                <fieldset>
                    <legend>Modifica Stato Pda</legend>
                    <label for="id">ID</label>
                    <input type="text" name="id" readonly value=<?= $riga[0] ?>>
                    <br>                    
                    <label for="descrizione">Descrizione</label>
                    <input type="text" name="descrizione" readonly value=<?="'".$riga[1]."'" ?>>
                    
                    <label for="fase">Fase</label>
                    <select id="fase" name="fase">
                        <option value="" <?php if($riga[2]==""){echo "selected";} ?>>-</option>
                        <option value="OK" <?php if($riga[2]=="OK"){echo "selected";} ?>>OK</option>
                        <option value="KO" <?php if($riga[2]=="KO"){echo "selected";} ?>>KO</option>
                        <option value="BKL" <?php if($riga[2]=="BKL"){echo "selected";} ?>>BKL</option>
                        <option value="BKLP" <?php if($riga[2]=="BKLP"){echo "selected";} ?>>BKLP</option>
                        <?=$riga[2]?>
                    </select>
                    <br>
                    <input type="submit" value="Aggiorna">
                    <input type="button" value="Elimina" onclick="location.href='queryEliminaStatoLuce.php?id='+<?=$id?>;">
                </fieldset>
            </form>



        </div>
    </body>
    <script>


        $(function () {
            $('#pesi td').on('click', function () {
                var row = $(this).closest('tr');
                var id = $(row).find('td').eq(0).html();
                window.location.href = 'modificaStatoLuce.php?id=' + id;
            });
        });
        
      
    </script>
</html>


