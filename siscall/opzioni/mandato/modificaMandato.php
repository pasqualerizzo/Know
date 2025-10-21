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

$query = "SELECT * FROM `mandato` where id='$id'";
$risultato = $conn19->query($query);
$riga = $risultato->fetch_array();
$conteggio = $risultato->num_rows;
?>

<html>
    <head>
        <title>MagePunti</title>
         <link href="../../../css/tabella.css" rel="stylesheet">
        <link href="../../../css/sidebar.css" rel="stylesheet">
        <script type='text/javascript' src='../../../elementi/js/jquery.min.js'></script>
        <script type='text/javascript' src='../../../elementi/js/script.js'></script>

    </head>
    <body>
        <header>
            <h1 class="titolo">Mandato:<br>Mod. dati Mandato</h1>
        </header>
        <div>
            <input type="hidden"  id="permessi" value=<?= $visualizzazione ?>>
        </div>
        <?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
        <div class="pagina">
            <form action="queryModificaMandato.php" method="POST">
                <fieldset>
                    <legend>Modifica Mandato</legend>
                    <label for="id">ID</label>
                    <input type="text" name="id" readonly value=<?= $riga[0] ?>>
                    <br>
                    <label for="data">Descrizione</label>
                    <input type="text" name="data"  value="<?= $riga[1] ?>">
                    <br>
                    <label for="tipo">Mandato</label>
                    <select id="tipo" name="tipo">
                        <option value="" <?php
                        if ($riga[2] == "") {
                            echo "selected";
                        }
                        ?>>-</option>
                        <option value="Plenitude" <?php
                        if ($riga[2] == "Plenitude") {
                            echo "selected";
                        }
                        ?>>Plenitude</option>
                        <option value="Enel Energia" <?php
                                if ($riga[2] == "Enel Energia") {
                                    echo "selected";
                                }
                                ?>>Enel Energia</option>
                        <option value="enel_out" <?php
                        if ($riga[2] == "enel_out") {
                            echo "selected";
                        }
                        ?>>Enel Out</option>
                        <option value="Green Network" <?php
                        if ($riga[2] == "Green Network") {
                            echo "selected";
                        }
                        ?>>Green Network</option>
                        <option value="Illumia" <?php
                                if ($riga[2] == "Illumia") {
                                    echo "selected";
                                }
                                ?>>Illumia</option>
                        <option value="Vivigas Energia" <?php
                        if ($riga[2] == "Vivigas Energia") {
                            echo "selected";
                        }
                                ?>>Vivigas Energia</option>
                        <option value="Vodafone" <?php
                                if ($riga[2] == "Vodafone") {
                                    echo "selected";
                                }
                                ?>>Vodafone</option>
                        <option value="Wind" <?php
                        if ($riga[2] == "Wind") {
                            echo "selected";
                        }
                        ?>>Wind</option>
                        <option value="Polizza" <?php
                        if ($riga[2] == "Polizza") {
                            echo "selected";
                        }
                        ?>>Polizza</option>
                        
                        
                         <option value="Iren" <?php
                        if ($riga[2] == "Iren") {
                            echo "selected";
                        }
                        ?>>Iren</option>
                                       
                         <option value="EnelIn" <?php
                        if ($riga[2] == "EnelIn") {
                            echo "selected";
                        }
                        ?>>EnelIn</option>
                         
                    </select>                    
                    <br>
                    <label for="tipoCampagna">Tipo Campagna</label>
                    <select id="tipoCampagna" name="tipoCampagna">
                        <option value="" <?php
                                if ($riga[3] == "") {
                                    echo "selected";
                                }
                        ?>>-</option>
                        <option value="Winback" <?php
                                if ($riga[3] == "Winback") {
                                    echo "selected";
                                }
                        ?>>Winback</option>
                        <option value="Valore" <?php
                                if ($riga[3] == "Valore") {
                                    echo "selected";
                                }
                        ?>>Valore</option>
                        <option value="Prospect" <?php
                                if ($riga[3] == "Prospect") {
                                    echo "selected";
                                }
                        ?>>Prospect</option>
                        <option value="Cross" <?php
                                if ($riga[3] == "Cross") {
                                    echo "selected";
                                }
                        ?>>Cross</option>
                        <option value="Amazon" <?php
                                if ($riga[3] == "Amazon") {
                                    echo "selected";
                                }
                        ?>>Amazon</option>
                        <option value="Polizza" <?php
                                if ($riga[3] == "Polizza") {
                                    echo "selected";
                                }
                        ?>>Polizza</option>
                        <option value="Lead" <?php
                                if ($riga[3] == "Lead") {
                                    echo "selected";
                                }
                        ?>>Lead</option>
                    </select>



                    <br>
                    <input type="submit" value="Aggiorna">
                </fieldset>
            </form>



        </div>
    </body>
    <script>

    </script>
</html>


