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

require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrm.php";
$objCrm = new ConnessioneCrm();
$connCrm = $objCrm->apriConnessioneCrm();

require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscall2.php";
$objS2 = new connessioneSiscall2();
$connS2 = $objS2->apriConnessioneSiscall2();

require "/Applications/MAMP/htdocs/Know/connessione/connessioneGt.php";
$objGt = new connessioneGt;
$connGt = $objGt->apriConnessioneGt();

require "/Applications/MAMP/htdocs/Know/connessione/connessioneDigital.php";
$objDgt = new connessioneDigital();
$connDgt = $objDgt->apriConnessioneDigital();

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj = new Connessione();
$conn19 = $obj->apriConnessione();

$id = filter_input(INPUT_GET, "id");
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
        . " id='$id' "
        . " AND "
        . " status='active' "
        . " AND "
        . " is_admin='off' "
        . " ORDER BY "
        . " rolename";

$risultato = $connCrm->query($query);
while ($riga = $risultato->fetch_array()) {
    $nome_completo = $riga["user_name"];
    $nome = $riga["first_name"];
    $cognome = $riga["last_name"];
    $ruolo = $riga["rolename"];
}
$queryLog = ""
        . " SELECT "
        . " * "
        . " FROM "
        . " vtiger_loginhistory "
        . " WHERE "
        . " user_name='$nome_completo' "
        . " ORDER BY `login_id` DESC  "
        . " LIMIT 10 "
        . " ";

$risultato = $connCrm->query($queryLog);

$queryLogSiscall = ""
        . " SELECT "
        . " vicidial_users.user, "
        . " event, "
        . " campaign_id, "
        . " event_date, "
        . " computer_ip, "
        . " vicidial_user_log.phone_login "
        . " FROM "
        . " vicidial_user_log "
        . " INNER JOIN vicidial_users ON vicidial_user_log.user=vicidial_users.user "
        . " WHERE "
        . " full_name='$nome_completo' "
        . " ORDER BY `user_log_id` DESC  "
        . " LIMIT 10 ";
//echo $queryLogSiscall;
$risultatoS2 = $connS2->query($queryLogSiscall);
$risultatoSGt = $connGt->query($queryLogSiscall);
$risultatoSDgt = $connDgt->query($queryLogSiscall);
$queryRicerca = ""
        . "Select * "
        . " From gestioneOperatori "
        . " Where nomeCompleto='$nome_completo'";
$risultato = $conn19->query($queryRicerca);
if ($risultato->num_rows == 0) {
    $dataCessazione = "";
} else {
    $rigaCessazione = $risultato->fetch_array();
    $dataCessazione = $rigaCessazione[2];
}
?>
<html>
    <head>
       <head>
        <title>Metrics</title>
         <link href="../../../css/tabella.css" rel="stylesheet">
        <link href="../../../css/sidebar.css" rel="stylesheet">
        <link rel="stylesheet" href="../style.css">
    </head>
    <body>
        <header>
            <h1 class="titolo">Modifica Operatore:<br><?= $nome_completo ?></h1>
        </header>
        <div>
            <input type="hidden"  id="permessi" value=<?= $visualizzazione ?>>
        </div>
<?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
        <div class="pagina">
            <form action="aggiungiData.php" method="POST" name="modulo">
                <fieldset>
                    <legend>Modifica Operatore</legend>
                    <label for="id">ID</label>
                    <input type="text" name="id" id="id" readonly value=<?= $id ?>>
                    <label for="nome_completo">Nome Completo:</label>
                    <input type="text" id="nome_completo" name="nome_completo" readonly value=<?= $nome_completo ?>>
                    <br>
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" readonly value=<?= $nome ?>>
                    <label for="cognome">Cognome</label>
                    <input type="text" id="cognome" name="cognome" readonly value="<?= $cognome ?>">
                    <br>
                    <label for="ruolo">Ruolo</label>
                    <input type="text" id="ruolo" name="ruolo"  readonly value="<?= $ruolo ?>" style="width: 100%">
                    <br>
                    <br>
                    <div style="display: flex;flex-direction: column;align-items: center;">
                    <label style="background-color: blue;color:white;width: 100%;display: flex;border:1px solid black;">Tabella Log Operatore Siscall2</label>
                    <table class="tabellaOperatore">
                        <tr>             
                            <th>User Name</th>
                            <th>Status</th>
                            <th>User Ip</th>
                            <th>LogIn Time</th>
                            <th>Phone</th>
                            <th>Campagna</th>
                        </tr>
<?php
while ($rigaLog = $risultatoS2->fetch_array()) {
    $usernameS2 = $rigaLog[0];
    echo "<tr>";
    echo "<td>$rigaLog[0]</td>";
    echo "<td>$rigaLog[1]</td>";
    echo "<td>$rigaLog[4]</td>";
    echo "<td>$rigaLog[3]</td>";
    echo "<td>$rigaLog[5]</td>";
    echo "<td>$rigaLog[2]</td>";
    echo "</tr>";
}
?>
                    </table>
                    </div>
                    <br>
                    <div style="display: flex;flex-direction: column;align-items: center;">
                    <label style="background-color: green;color:white;width: 100%;display: flex;border:1px solid black;">Tabella Log Operatore Siscall Gt</label>
                    <table class="tabellaOperatore" >
                        <tr>             
                            <th>User Name</th>
                            <th>Status</th>
                            <th>User Ip</th>
                            <th>LogIn Time</th>
                            <th>Phone</th>
                            <th>Campagna</th>
                        </tr>
<?php
while ($rigaLog = $risultatoSGt->fetch_array()) {
    $usernameSGt = $rigaLog[0];
    echo "<tr>";
    echo "<td>$rigaLog[0]</td>";
    echo "<td>$rigaLog[1]</td>";
    echo "<td>$rigaLog[4]</td>";
    echo "<td>$rigaLog[3]</td>";
    echo "<td>$rigaLog[5]</td>";
    echo "<td>$rigaLog[2]</td>";
    echo "</tr>";
}
$username = $usernameS2 ?? $usernameSGt;
?>
                    </table>
                    </div>
                    <br>
                    
                    <div style="display: flex;flex-direction: column;align-items: center;">
                    <label style="background-color: brown; color: white;width: 100%;display: flex;border:1px solid black;">Tabella Log Operatore Siscall Digital</label>
                    <table class="tabellaOperatore" >
                        <tr >             
                            <th>User Name</th>
                            <th>Status</th>
                            <th>User Ip</th>
                            <th>LogIn Time</th>
                            <th>Phone</th>
                            <th>Campagna</th>
                        </tr>
<?php
while ($rigaLog = $risultatoSDgt->fetch_array()) {
    echo "<tr>";
    echo "<td>$rigaLog[0]</td>";
    echo "<td>$rigaLog[1]</td>";
    echo "<td>$rigaLog[4]</td>";
    echo "<td>$rigaLog[3]</td>";
    echo "<td>$rigaLog[5]</td>";
    echo "<td>$rigaLog[2]</td>";
    echo "</tr>";
}
?>
                    </table>
                    </div>
                        <br>
                      <div style="display: flex;flex-direction: column;align-items: center;">
                    </table>
                    <br>
                    <br>
                    <label style="background-color: gold; color: black;width: 100%;display: flex;border:1px solid black;">Tabella Log Operatore CRM</label>
                    <table class="tabellaOperatore">
                        <tr>
                            <th>Login_ID</th>
                            <th>User Name</th>
                            <th>User Ip</th>
                            <th>LogOut Time</th>
                            <th>LogIn Time</th>
                            <th>Status</th>
                        </tr>
<?php
while ($rigaLog = $risultato->fetch_array()) {
    echo "<tr>";
    echo "<td>$rigaLog[0]</td>";
    echo "<td>$rigaLog[1]</td>";
    echo "<td>$rigaLog[2]</td>";
    echo "<td>$rigaLog[4]</td>";
    echo "<td>$rigaLog[3]</td>";
    echo "<td>$rigaLog[5]</td>";
    echo "</tr>";
}
?>
                    </table>
                      </div>
                    <br>
                       <div style="border: 1px solid black">
                           <label>UserName
                               <input type="text" id="username" value="<?= $username ?>"> </label>
                           <br>
            <label>Data Cessazione Contratto
                <input type="date" id="dataCessazione" name="dataCessazione" value="<?=$dataCessazione?>">
            </label>
                           <br>
        </div>
                    <br>
                    
                    <input type="submit" value="Aggiorna">
                    <input type="submit" formaction="../creaTabellaOperatore.php" style="background-color: red;color: white" value="Indietro">
                </fieldset>
            </form>
        </div>
        
     
    </body>
    <script>

    </script>
</html>