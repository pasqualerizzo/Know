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

$queryMese = "SELECT mese FROM `stringheSiscallGT` group by mese";
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

$query = "SELECT userGroup,mandato,SUM(pause),SUM(wait),SUM(talk),SUM(dispo),SUM(numero),Sum(dead) FROM `stringheSiscallGT`where mese='$meseSelezionato' group by userGroup";
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
    <div class="titolo">
        <h1>Siscall:<br>User Group</h1>
    </div>
</header>
<div>
    <input type="hidden" id="permessi" value=<?= $visualizzazione ?>>
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
        <button onclick="scaricaCSV()">Scarica CSV</button>
    </div>
    <div>
        <table class='blueTable'>
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


    function redirigi(sel) {
        var data = sel.options[sel.selectedIndex].value;
        window.location.href = "vistaMeseGroup.php?meseSelezionato=" + data;

    }


    function scaricaCSV() {
        <?php echo "var valore= '$meseSelezionato'" ?>
        window.location.href = "exportVistaMeseUserGroup.php?meseSelezionato=" + valore;
    }
</script>
</html>


