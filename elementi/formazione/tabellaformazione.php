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

$query = "SELECT * FROM `formazioneTotale` order by user,id asc";
$risultato = $conn19->query($query);
$conteggio = $risultato->num_rows;
$intestazione = $risultato->fetch_fields();
$el = 0;
?>

<html>
    <head>
        <title>MagePunti</title>
         <link href="../../css/tabella.css" rel="stylesheet">
        <link href="../../css/sidebar.css" rel="stylesheet">
        <script type='text/javascript' src='../js/jquery.min.js'></script>
        <script type='text/javascript' src='../js/script.js'></script>

    </head>
    <body>
        <header>
            <h1 class="titolo">Formazione:<br>Elenco Parziale</h1>
        </header>
        <div>
            <input type="hidden"  id="permessi" value=<?= $visualizzazione ?>>
        </div>
        <?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
        <div class="pagina">
            <div>
                <table class='blueTable' id="pesi" >
                    <thead>
                        <tr>
                            <?php
                            foreach ($intestazione as $info) {
                                echo "<th>" . $info->name . "</th>";
                                $el++;
                            }
                            ?>
                        </tr>
                        
                    </thead>
                    <tbody>
                        <?php
                        while ($lista = $risultato->fetch_array()) {
                            echo '<tr>';
                            echo "<td>" . $lista[0] . "</td>";
                            echo "<td>" . $lista[1] . "</td>";
                            echo "<td>" . $lista[2] . "</td>";
                            if($lista[3]>42){$p=" style="."'background-color: red'";}else{$p="";}
                            echo "<td". $p .">" . $lista[3] . "</td>";
                            echo "<td>" . $lista[4] . "</td>";
                            echo "<td>" . $lista[5] . "</td>";
                            echo "<td>" . $lista[6] . "</td>";
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
    <script>

    </script>
</html>


