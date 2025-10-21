<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();
?>


<html>
    <head>
        <title>MagiPunteggi-registrazione</title>
        <link href="../css/style.css" rel="stylesheet">
        <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
    </head>
    <body class="raccolta">
        <header>          
            <h1 class="titolo">
                <img src="../images/favicon.png" class="immaggine">
                MagiPunteggi Registrazione
                <img src="../images/favicon.png" class="immaggine">
            </h1>
        </header>
        <form method="post" action="registrazione.php">
            <fieldset class="raccoltaDati">
                <legend>Log In</legend>
                <label for="username"> Nome Utente
                    <input type="text" name="username" id="username" required>
                </label>
                
                <label for="password">Password
                    <input type="password" name="password" id="password" required>
                </label>
                <label for="livello">Livello
                    <select id="livello" name="livello">
                        <option value="1">1</option>
                        <option value="9">9</option>
                    </select>
                </label>
                <input type="submit" value="login">
            </fieldset>
        </form>
    </body>
</html>


