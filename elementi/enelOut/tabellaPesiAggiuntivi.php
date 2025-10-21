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

$queryMese = "SELECT dataInizioValidita FROM `enelOutPesiAggiuntivi` GROUP by dataInizioValidita ORDER by dataInizioValidita DESC";
$risultatoMese = $conn19->query($queryMese);
$i = 0;
while ($mesi = $risultatoMese->fetch_Array()) {
    $elencoMesi[] = $mesi[0];
    $i++;
}

if (isset($_GET["meseSelezionato"])) {
    $meseSelezionato = $_GET["meseSelezionato"];
} else {
    $meseSelezionato = $elencoMesi[0];
}

$query = "SELECT * FROM `enelOutPesiAggiuntivi` where dataInizioValidita='$meseSelezionato'";
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
            <h1 class="titolo">Enel Out:<br>Pesi Aggiuntivi</h1>
        </header>
        <div>
            <input type="hidden"  id="permessi" value=<?= $visualizzazione ?>>
        </div>
        <?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
        <div class="pagina">
            <div>
                <p>Conteggio Totale: <?= $conteggio ?></p>
                <label for="mesi">Selezione mese</label>
                <select id="mesi" onchange="redirigi(this)">

                    <?php
                    foreach ($elencoMesi as $valoreMese) {
                        echo "<option value=" . $valoreMese;
                        if ($valoreMese == $meseSelezionato) {
                            echo " selected";
                        }
                        echo " >" . $valoreMese . "</option>";
                    }
                    ?>
                </select>
                <button onclick="aggiungiMese()">Aggiungi Mese</button>
            </div>
            <div>
                <table class='blueTable' id="pesi">
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
                            for ($i = 0; $i < $el; $i++) {

                                echo "<td>" . $lista[$i] . "</td>";
                            }
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>

            </div>
        </div>

    </body>
    <script>


        function aggiungiMese() {

            window.location.href = "aggiungiMesePesoAggiuntivi.php";
        }
        
          function redirigi(sel) {
            var data = sel.options[sel.selectedIndex].value;
            window.location.href = "tabellaPesiAggiuntivi.php?meseSelezionato=" + data;

        }

        $(function () {
            $('#pesi td').on('click', function () {
                var row = $(this).closest('tr');
                var id = $(row).find('td').eq(0).html();
                window.location.href = 'modificaPesiAggiuntivi.php?id=' + id;
            });
        });
    </script>
</html>


