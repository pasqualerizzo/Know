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

$query = "SELECT * FROM `unionCampagna` where id='$id'";
$risultato = $conn19->query($query);
$riga = $risultato->fetch_array();

$conteggio = $risultato->num_rows;
?>

<html>
    <head>
        <title>MagePunti: Union Group</title>
         <link href="../../../css/tabella.css" rel="stylesheet">
        <link href="../../../css/sidebar.css" rel="stylesheet">
        <script type='text/javascript' src='../../js/jquery.min.js'></script>
        <script type='text/javascript' src='../../js/script.js'></script>

    </head>
    <body>
        <header>
            <h1 class="titolo">Union:<br>Mod. Campagna</h1>
        </header>
        <div>
            <input type="hidden"  id="permessi" value=<?= $visualizzazione ?>>
        </div>
        <?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
        <div class="pagina">
            <form action="queryModificaCampagna.php" method="POST">
                <fieldset>
                    <legend>Modifica Campagna</legend>
                    <label for="id">ID</label>
                    <input type="text" name="id" readonly value=<?= $riga[0] ?>>
                    <br>                    
                    <label for="descrizione">Nome</label>
                    <input type="text" name="descrizione" readonly value=<?="'".$riga[1]."'" ?>>
                    
                    <label for="fase">Tipo</label>
                    <select id="fase" name="fase">
                        <option value="" <?php if($riga[2]==""){echo "selected";} ?>>-</option>
                        <option value="Winback" <?php if($riga[2]=="Winback"){echo "selected";} ?>>Winback</option>
                        <option value="Valore" <?php if($riga[2]=="Valore"){echo "selected";} ?>>Valore</option>
                        <option value="Prospect" <?php if($riga[2]=="Prospect"){echo "selected";} ?>>Prospect</option>
                        <option value="Cross" <?php if($riga[2]=="Cross"){echo "selected";} ?>>Cross</option>
                        <option value="Lead" <?php if($riga[2]=="Lead"){echo "selected";} ?>>Lead</option>
                        <option value="Amazon" <?php if($riga[2]=="Amazon"){echo "selected";} ?>>Amazon</option>
                        
                        <?=$riga[2]?>
                    </select>
                    <br>
                    <input type="submit" value="Aggiorna">
                    
                </fieldset>
            </form>



        </div>
    </body>
    <script>


        $(function () {
            $('#pesi td').on('click', function () {
                var row = $(this).closest('tr');
                var id = $(row).find('td').eq(0).html();
                window.location.href = 'modificaCampagna.php?id=' + id;
            });
        });
        
      
    </script>
</html>


