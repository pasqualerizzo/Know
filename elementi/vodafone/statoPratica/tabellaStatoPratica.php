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

$query = "SELECT * FROM `vodafoneStatoPratica` order by descrizione asc";
$risultato = $conn19->query($query);
$conteggio = $risultato->num_rows;
$intestazione = $risultato->fetch_fields();
$el = 0;
?>

<html>
    <head>
        <title>MagePunti</title>
         <link href="../../../css/tabella.css" rel="stylesheet">
        <link href="../../../css/sidebar.css" rel="stylesheet">


    </head>
    <body>
        <header>
            <h1 class="titolo">Vodafone:<br>Stato Pratica</h1>
        </header>
         <div>
            <input type="hidden"  id="permessi" value=<?= $visualizzazione ?>>
        </div>
<?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
        <div class="pagina">
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
    echo "<td style='width:20px;height:20px'>"
    . "<button  onclick='editRow(this)'>"
    . "<img style='width:20px;height:20px' src='../../../images/edit.png' alt='Modifica'>"
    . "</button>"
    . "</td>"
    . "</tr>";
}
?>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
    <script>


                                           function editRow(button) {
    // Trova la riga della tabella che contiene il pulsante cliccato
    var row = button.parentNode.parentNode;    
    // Seleziona il primo elemento della riga
    var id = row.cells[0].innerText; // Utilizzando l'indice
    window.location.href = 'modificaStatoPratica.php?id=' + id;
    
    }
    </script>
</html>


