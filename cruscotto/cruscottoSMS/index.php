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


include "/Applications/MAMP/htdocs/Know/cruscotto/js/funzioniCruscotto.php";

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();



?>

<html>
    <head>
        <title>Metrics: Cruscotto SMS</title>
        <link href="../../css/tabella.css" rel="stylesheet">
        <link href="../../css/sidebar.css" rel="stylesheet">
       
        <link rel="icon" href="../../images/logo-metrics.png" type="image/x-icon">
        
    </head>
    <body style="font-family: poppins;" >
        <header>
            <h1>Metrics: Cruscotto SMS</h1>
        </header>
<?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
        <div>
            <input type="hidden"  id="permessi" value=<?= $visualizzazione ?>>
        </div>
        <div class="container2">
            <form action="">
                <div style="display: flex;flex-wrap: nowrap;align-content: flex-start;align-items: center;justify-content: space-around;">

                    <div style="display: flex;width: 25%;align-content: space-between;align-items: stretch;justify-content: space-between;flex-direction: column;flex-wrap: nowrap;">
                        <label for="mese">Mese :
                            <input type="month" id="mese" name="mese" ">
                        </label>
                        
                    </div>

                    
                    
<!--                    <div style="display: flex;width: 25%;align-content: center;align-items: center;justify-content: space-evenly;">
                        <label>Categoria:</label>
                        <select  size="4" multiple id="categoria" >

                        </select>                        
                    </div>-->
                    



                    <div style="display: flex;flex-direction: column;">                       
                        <button style="background-color: aquamarine; padding:10px 20px; cursor:pointer; border:none; border-radius: 4px; font-size: larger;" type="button" 
                                onclick="creaTabellaSMS()">Stato WA</button> 
                    </div>


<div style="display: flex;flex-direction: column;">                       
                        <button style="background-color: greenyellow; padding:10px 20px; cursor:pointer; border:none; border-radius: 4px; font-size: larger;" type="button" 
                                onclick="window.location.href = 'campagna/listaCampagna.php'">Lista Campagna</button> 
                    </div>
                </div>
            </form>
        </div>
        <div>
            <a id="tabella" style="display:flex;flex-wrap: nowrap;align-items: flex-start;flex-direction: column;align-content: center;justify-content: space-between;"></a>
        </div>
        <br>
        <div >
            <a id="PdpInterno" style="display:flex;flex-wrap: nowrap;align-items: flex-start;flex-direction: row;align-content: center;justify-content: space-between;"></a>
        </div>
        <br>
        <div >
            <a id="PdpEsterno" style="display:flex;flex-wrap: nowrap;align-items: flex-start;flex-direction: row;align-content: center;justify-content: space-between;"></a>
        </div>
    </body>
    <script>
<?php
include "funzioni/funzioniCruscotto.js";
?>

    </script>
</html>