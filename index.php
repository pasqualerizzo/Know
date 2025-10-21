 <?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$localIP = $_SERVER['REMOTE_ADDR'];
$host = $_SERVER['SERVER_NAME'];
$ip = "0.0.0.0";



require __DIR__ . "/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();
if (isset($_GET["errore"])) {
    $stato = $_GET["errore"];
} else {
    $stato = "";
}
$pagina = "";
switch ($stato) {
    case "password":
        $pagina = "<p>"
                . "Password Errata!"
                . "</p>";
        break;
    case "logged":
        $pagina = "<p>"
                . "Effettua l'accesso per continuare!"
                . "</p>";
        break;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metrics | Analisi Dati</title>
    <link href="css/loginpage.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">

    <style>
        
    </style>
</head>
<body >
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <img src="images/logo-metrics.png" alt="Logo">
            </div>
            <form method="post" action="login/login.php">
                <div class="form-group">
                    <label for="username">Nome Utente</label>
                    <input placeholder="Inserisci Username" type="text" class="form-control" name="username" id="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input placeholder="Inserisci Password" type="password" class="form-control" name="password" id="password" required>
                </div>
                <input type="hidden" name="ip" id="ip" value="<?= $ip ?>">
                <div class="messaggio">
                    <?= $pagina ?>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
        </div>
    </div>
  
</body>
</html>



