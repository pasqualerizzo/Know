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

$listaEsclusi = "("
        . "'',"
        . "'BO',"
        . "'TL',"
        . "'-',"
        . "'RU'"
        . ")";

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$queryMese = "SELECT mese FROM `calendario`   GROUP by mese ORDER by mese DESC";
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



$query = "SELECT "
        . "id as id, "
        . "giorno AS 'Giorno',"
        . " mese,"
        . " peso"
        . " FROM "
        . " calendario "
        . " where "
        . " mese='$meseSelezionato'";

//echo $query;
$risultato = $conn19->query($query);
$conteggio = $risultato->num_rows;
$intestazione = $risultato->fetch_fields();
$el = 0;
?>

<html>
    <head>
        <title>Pagamento: Calendario</title>
         <link href="../../../css/tabella.css" rel="stylesheet">
        <link href="../../../css/sidebar.css" rel="stylesheet">


    </head>
    <body>
        <header>
            <h1 class="titolo">Pagamento:<br>Calendario</h1>

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
                <button onclick="scaricaPagamento()">Scarica CSV</button>
            </div>
            <div>
                <table class='blueTable' id="tabella">
                    <thead>
                        <tr>
                            <tr>
                                <th>ID</th>
        <th>Giorno</th>
        <th>mese</th>
        <th>Peso</th>
                            
        
                        </tr>
                    </thead>
                    <tbody>
<?php
while ($lista = $risultato->fetch_array()) {
    echo '<tr>';
    for ($i = 0; $i < 4; $i++) {

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

        function redirigi(sel) {
            var data = sel.options[sel.selectedIndex].value;
            window.location.href = "tabellaPagamentoOperatore.php?meseSelezionato=" + data;
}

function sortTableNumero(n) {
    var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    table = document.getElementById("tabella");
    switching = true;
    // Set the sorting direction to ascending:
    dir = "asc";
    /* Make a loop that will continue until
     no switching has been done: */
    while (switching) {
        // Start by saying: no switching is done:
        switching = false;
        rows = table.rows;
        /* Loop through all table rows (except the
         first, which contains table headers): */
        for (i = 1; i < (rows.length - 1); i++) {
            // Start by saying there should be no switching:
            shouldSwitch = false;
            /* Get the two elements you want to compare,
             one from current row and one from the next: */
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];
            /* Check if the two rows should switch place,
             based on the direction, asc or desc: */
            if (dir == "asc") {
                if (Number(x.innerHTML) > Number(y.innerHTML)) {
                    shouldSwitch = true;
                    break;
                }
            } else if (dir == "desc") {
                if (Number(x.innerHTML) < Number(y.innerHTML)) {
                    shouldSwitch = true;
                    break;
                }
            }
        }
        if (shouldSwitch) {
            /* If a switch has been marked, make the switch
             and mark that a switch has been done: */
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            // Each time a switch is done, increase this count by 1:
            switchcount++;
        } else {
            /* If no switching has been done AND the direction is "asc",
             set the direction to "desc" and run the while loop again. */
            if (switchcount == 0 && dir == "asc") {
                dir = "desc";
                switching = true;
            }
        }
    }
}

function sortTable(n) {
    var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    table = document.getElementById("tabella");
    switching = true;
    // Set the sorting direction to ascending:
    dir = "asc";
    /* Make a loop that will continue until
     no switching has been done: */
    while (switching) {
        // Start by saying: no switching is done:
        switching = false;
        rows = table.rows;
        /* Loop through all table rows (except the
         first, which contains table headers): */
        for (i = 1; i < (rows.length - 1); i++) {
            // Start by saying there should be no switching:
            shouldSwitch = false;
            /* Get the two elements you want to compare,
             one from current row and one from the next: */
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];
            /* Check if the two rows should switch place,
             based on the direction, asc or desc: */
            if (dir == "asc") {
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                    // If so, mark as a switch and break the loop:
                    shouldSwitch = true;
                    break;
                }
            } else if (dir == "desc") {
                if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                    // If so, mark as a switch and break the loop:
                    shouldSwitch = true;
                    break;
                }
            }
        }
        if (shouldSwitch) {
            /* If a switch has been marked, make the switch
             and mark that a switch has been done: */
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            // Each time a switch is done, increase this count by 1:
            switchcount++;
        } else {
            /* If no switching has been done AND the direction is "asc",
             set the direction to "desc" and run the while loop again. */
            if (switchcount == 0 && dir == "asc") {
                dir = "desc";
                switching = true;
            }
        }
    }
}

function scaricaPagamento() {
            var mesi = document.getElementById("mesi");
            var valore = mesi.options[mesi.selectedIndex].value;
            window.location.href = "exportPagamenti.php?meseSelezionato=" + valore;
        }
        
        function editRow(button) {
    // Trova la riga della tabella che contiene il pulsante cliccato
    var row = button.parentNode.parentNode;    
    // Seleziona il primo elemento della riga
    var id = row.cells[0].innerText; // Utilizzando l'indice
    window.location.href = 'query/modificaCalendario.php?id=' + id;
        }

    </script>
</html>


