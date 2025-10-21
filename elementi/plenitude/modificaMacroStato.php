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

$query = "SELECT * FROM `plenitudeMacroStato` where id='$id'";
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
            <h1 class="titolo">Plenitude:<br>Mod Macro Stato</h1>
        </header>
        <div>
            <input type="hidden"  id="permessi" value=<?= $visualizzazione ?>>
        </div>
        <?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
        <div class="pagina">
            <form action="queryModificaMacroStato.php" method="POST">
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
                        <option value="KO DA RECUPERARE ENI" <?php if($riga[2]=="KO DA RECUPERARE ENI"){echo "selected";} ?>>KO DA RECUPERARE ENI</option>
                        <option value="KO Gia attivo" <?php if($riga[2]=="KO Gia attivo"){echo "selected";} ?>>KO Gia attivo</option>
                        <option value="KO Interno" <?php if($riga[2]=="KO Interno"){echo "selected";} ?>>KO Interno</option>
                        <option value="KO Moroso" <?php if($riga[2]=="KO Moroso"){echo "selected";} ?>>KO Moroso</option>
                        <option value="KO Precheck" <?php if($riga[2]=="KO Precheck"){echo "selected";} ?>>KO Precheck</option>
                        <option value="KO Ripensamento" <?php if($riga[2]=="KO Ripensamento"){echo "selected";} ?>>KO Ripensamento</option>
                        <option value="KO SUBENTRO" <?php if($riga[2]=="KO SUBENTRO"){echo "selected";} ?>>KO SUBENTRO</option>
                        <option value="KO TARIFFA" <?php if($riga[2]=="KO TARIFFA"){echo "selected";} ?>>KO TARIFFA</option>
                        <option value="KO Tipo acquisizione" <?php if($riga[2]=="KO Tipo acquisizione"){echo "selected";} ?>>KO Tipo acquisizione</option>
                        <option value="KO Voltura" <?php if($riga[2]=="KO Voltura"){echo "selected";} ?>>KO Voltura</option>
                        <option value="No motivazione" <?php if($riga[2]=="No motivazione"){echo "selected";} ?>>No motivazione</option>
                        <option value="SW Prevalente" <?php if($riga[2]=="SW Prevalente"){echo "selected";} ?>>SW Prevalente</option>
                        <option value="Winback" <?php if($riga[2]=="Winback"){echo "selected";} ?>>Winback</option>
                        <?=$riga[2]?>
                    </select>
                    <br>
                    <input type="submit" value="Aggiorna">
                    <input type="button" value="Elimina" onclick="location.href='queryEliminaMacroStato.php?id='+<?=$id?>;">
                </fieldset>
            </form>



        </div>
    </body>
    <script>


        $(function () {
            $('#pesi td').on('click', function () {
                var row = $(this).closest('tr');
                var id = $(row).find('td').eq(0).html();
                window.location.href = 'modificaMacroStato.php?id=' + id;
            });
        });
        
      
    </script>
</html>


