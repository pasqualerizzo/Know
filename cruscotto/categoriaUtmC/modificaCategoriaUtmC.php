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

$query = "SELECT * FROM `categoriaCampagna` where id='$id'";
$risultato = $conn19->query($query);
$riga = $risultato->fetch_array();

$conteggio = $risultato->num_rows;
?>

<html>
   <head>
        <title>Lead: Mod. Categoria UTM Campagna</title>
         <link href="../../css/tabella.css" rel="stylesheet">
        <link href="../../css/sidebar.css" rel="stylesheet">
 

    </head>
    <body>
        <header>
            <h1 class="titolo">Lead:<br>Mod. Categoria UTM Campagna</h1>
        </header>
        <div>
            <input type="hidden"  id="permessi" value=<?= $visualizzazione ?>>
        </div>
        <?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
        <div class="pagina">
            <form action="queryModificaCategoriaUtmC.php" method="POST">
                <fieldset>
                    <legend>Modifica Stato Pda</legend>
                    <label for="id">ID</label>
                    <input type="text" name="id" readonly size='20' value=<?= $riga[0] ?>>
                    <br>                    
                    <label for="descrizione">Descrizione</label>
                    <input type="text" name="UTM Campagna" size='75'  readonly value=<?="'".$riga[1]."'" ?>>
                    <br>
                    <label for="fase">Categoria</label>
                    <select id="fase" name="fase">
                        <option value="" <?php if($riga[2]==""){echo "selected";} ?>>-</option>
                        <option value="Energetico" <?php if($riga[2]=="Energetico"){echo "selected";} ?>>Energetico</option>
                        <option value="Telco" <?php if($riga[2]=="Telco"){echo "selected";} ?>>Telco</option>
                        
                        <?=$riga[2]?>
                    </select>
                    <br>
                    <input type="submit" value="Aggiorna">
                    <input type="button" value="Elimina" onclick="location.href='queryEliminaCategoriaUtmC.php?id='+<?=$id?>;">
                </fieldset>
            </form>



        </div>
    </body>
    <script>


        $(function () {
            $('#pesi td').on('click', function () {
                var row = $(this).closest('tr');
                var id = $(row).find('td').eq(0).html();
                window.location.href = 'modificaStatoPda.php?id=' + id;
            });
        });
        
      
    </script>
</html>


