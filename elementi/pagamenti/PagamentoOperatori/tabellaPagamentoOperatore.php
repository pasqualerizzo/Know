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

$queryMese = "SELECT dataRiferimento FROM `aggiuntaPagamento` where dataRiferimento>='2024-08-01'  GROUP by dataRiferimento ORDER by dataRiferimento DESC";
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
        . " nomeCompleto AS 'Operatore',"
        . " mese,"
        . " livello,"
        . " sede,"
        . " if(round((numero)+(orePolizze)-formazione,2)<=0,0,round((numero)+(orePolizze)-formazione,2)) as 'Ore Fatte', "
        . " round(orePagabili,2) as 'Ore Pagabili',"
        . " oreAutorizzate as 'Ore Autorizzate',"
        . " round(vodafonePesoPagato+vivigasPesoPagato+plenitudePesoPagato+greenPesoPagato+enelOutPesoPagato+irenPesoPagato+unionPesoPagato+enelPesoPagato+plenitudePolizzePesoPagato,2) as 'Peso Pagato',"
        . " round(vodafonePesoFormazione+vivigasPesoFormazione+plenitudePesoFormazione+greenPesoFormazione+enelOutPesoFormazione+irenPesoFormazione+unionPesoFormazione+enelPesoFormazione+plenitudePolizzePesoFormazione,2) as 'Peso Formazione',"
        . " puntiPagabili as 'Punti Pagabili',"
        . " valoreOre as 'Valore Ore',"
        . " costoOre as 'Costo Ore',"
        . " valorePezzi as 'Valore Pezzi',"
        . " costoPezzi as 'Costo Pezzi',"
        . " costoTotale as 'Costo Totale',"
        . " giorniLavorati as 'Giorni Lavorabili Mese',"
        . " round(costoGiorni,2) as 'Costo Giornaliero Risorsa',"
        . " round(costoAzienda,2) as 'Costo Azienda(1.4)'"
        . " FROM "
        . " pagamentoMese "
        . " inner join `aggiuntaPagamento` on pagamentoMese.id=aggiuntaPagamento.id "
        . " where "
        . " dataRiferimento='$meseSelezionato'"
        . " and sede not in $listaEsclusi ";
//echo $query;
$risultato = $conn19->query($query);
$conteggio = $risultato->num_rows;
$intestazione = $risultato->fetch_fields();
$el = 0;
?>

<html>
    <head>
        <title>Pagamento: Gara Ore</title>
         <link href="../../../css/tabella.css" rel="stylesheet">
        <link href="../../../css/sidebar.css" rel="stylesheet">


    </head>
    <body>
        <header>
            <h1 class="titolo">Pagamento:<br>Gara Ore</h1>

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
        <th>Operatore</th>
        <th>mese</th>
        <th>livello</th>
        <th>sede</th>
        <th>Ore Fatte</th>
        <th>Ore Pagabili</th>
        <th>Ore Autorizzate</th>
        <th>Peso Pagato</th>
        <th>Peso Formazione</th>
        <th>Punti Pagabili</th>
        <th>Valore Ore</th>
        <th>Costo Ore</th>
        <th>Valore Pezzi</th>
        <th>Costo Pezzi</th>
        <th>Costo Totale</th>
        <th>Giorni Lavorabili Mese</th>
        <th>Costo Giornaliero Risorsa</th>
        <th>Costo Azienda(1.4)</th>                            
        
                        </tr>
                    </thead>
                    <tbody>
        <?php
        while ($lista = $risultato->fetch_array()) {
            echo '<tr>';
            for ($i = 0; $i < 18; $i++) {

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

    </script>
</html>


