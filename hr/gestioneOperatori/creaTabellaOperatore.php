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
$obj = new Connessione();
$conn = $obj->apriConnessione();

require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrm.php";
$objCrm = new ConnessioneCrm();
$connCrm = $objCrm->apriConnessioneCrm();

$queryRuolo = ""
        . " SELECT "
        . " rolename "
        . " FROM "
        . " vtiger_role";

$risultaoRuolo = $connCrm->query($queryRuolo);
$ruoli = [];
while ($rigaRuolo = $risultaoRuolo->fetch_array()) {
    if (strlen($queryRuolo) > 16) {
        $parole = explode(" ", $rigaRuolo[0]);
        $sede = end($parole);
        if (!in_array($sede, $ruoli)) {
            $ruoli[] = $sede;
        }
    } else {
        $ruoli[] = $rigaRuolo[0];
    }
}

$query = ""
        . " SELECT "
        . " id, "
        . " user_name, "
        . " first_name, "
        . " last_name, "
        . " rolename "
        . " FROM "
        . " vtiger_users as user"
        . " INNER JOIN vtiger_user2role AS u2r ON user.id=u2r.userid "
        . " INNER JOIN vtiger_role AS ruolo ON ruolo.roleid=u2r.roleid"
        . " WHERE "
        . " status='active' "
        . " AND "
        . " is_admin='off' "
        . " ORDER BY "
        . " rolename";

$risultato = $connCrm->query($query);
$operatori = [];

while ($riga = $risultato->fetch_array()) {
    $temp = [];
    $temp["id"] = $riga["id"];
    $temp["nome_completo"] = $riga["user_name"];
    $temp["nome"] = $riga["first_name"];
    $temp["cognome"] = $riga["last_name"];
    $temp["ruolo"] = $riga["rolename"];
    $userName = $riga["user_name"];
    $queryUser = "SELECT user FROM `stringheTotale` where nomeCompleto='$userName'";
    $risultatoUser = $conn->query($queryUser);

    $operatori[] = $temp;
}
?>

    <!DOCTYPE html>
    <html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Struttura con Linguette</title>
        <link rel="stylesheet" href="style.css">
        <link href="../../css/tabella.css" rel="stylesheet">
        <link href="../../css/sidebar.css" rel="stylesheet">
        <script src="script.js"></script>
    </head>
    <body>
        <div>
            <input type="hidden"  id="permessi" value=<?= $visualizzazione ?>>
        </div>
<?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
        <div class="tabs" id="pulsantiera">
<?php
foreach ($ruoli as $ruolo) {
    //echo $ruolo;
    echo "<button class ='tablink' onclick ='filterTable(\"$ruolo\",this);'>$ruolo</button>";
}
?>
        </div>
 <div class="tabsRicerca">
     <br>
     <div class="tabsRicerca">
     <label>Ricerca per UserName:  </label>
     <br>
     <input type="text" id="searchInput" placeholder="Cerca per nome..." oninput="searchByName();">    
     </div>
     <div class="tabsRicerca">
     <label>Exporta CSV visualizzato:  </label>
     <br>
     <button onclick="exportCSV();">Export CSV </button>    
     </div>
         <div class="tabsRicerca">
             <form class="tabsRicercaForm" action="importCsv/importCsvOperatore.php" method="POST" enctype="multipart/form-data">
     <label>Importa file CSV date Cessazione Operatori:  </label>
     <br>
     <input type="file" name="importCSVOperatori" accept=".csv" >
     <br/>
     <input type="submit" value="import CSV" name="importCSv">  
             </form>
     </div>
    </div>        
        
            <br>
            <div>
            <table class="tabellaOperatore" id="tabellaOperatore">                
                    <tr>
                        <th>id</th>
                        <th>Nome Completo</th>
                        <th>Nome</th>
                        <th>Cognome</th>
                        <th>Ruolo</th>  
                        <th></th>
                    </tr>
<?php
foreach ($operatori as $operatore) {
    echo "<tr id='riga'>";
    echo "<td>$operatore[id]</td>";
    echo "<td>$operatore[nome_completo]</td>";
    echo "<td>$operatore[nome]</td>";
    echo "<td>$operatore[cognome]</td>";
    echo "<td>$operatore[ruolo]</td>";
    echo "<td style='width:20px;height:20px'>"
    . "<button  onclick='editRow(this)'>"
    . "<img style='width:20px;height:20px' src='../../images/edit.png' alt='Modifica'>"
    . "</button>"
    . "</td>"
    . "</tr>";
}
?>            
            </table>
            
        </div>         
        </html>

