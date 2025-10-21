<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


include 'db.php';
$sql ="SELECT dataOra FROM accessi";
$stmt = $conn->prepare($sql);
try {
    
    $stmt->execute();
    $publishers = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($publishers) {
        $scadenza= $publishers["dataOra"];
    }
}catch (Exception $e){
    $conn->rollback();
    throw $e;
}
$conn = null;
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica Outh Meta</title>
    <link rel="stylesheet" href="style.css">
  </head>
<body> 
    <div class="divForm">
<form action="verify.php" method="post">
  <div class="input">
    <label for="AuthMeta" class="form-label">Nuovo Auth Meta</label><br>
    <input type="text" class="input-form" name="authMeta" required>
  </div>
  <div class="input">
    <label for="Password" class="form-label">Password</label><br>
    <input type="password" class="input-form" name="psw" required>
  </div class="input">
  <button type="submit" class="button-form">Aggiorna Auth</button>
</form>
<div class="scadenza">
    Scadenza Auth: <u><?php echo $scadenza; ?></u>
    </div>
</div>
<div class="divForm">
<form action="verify.php" method="post">
  <div class="input">
    <label for="newPassword" class="form-label">Nuova Password</label><br>
    <input type="text" class="input-form" name="newPsw" required>
  </div>
  <div class="input">
    <label for="Password" class="form-label">Vecchia Password</label><br>
    <input type="password" class="input-form" name="psw" required>
  </div class="input">
  <button type="submit" class="button-form">Aggiorna Password</button>
</form>
</div>
<div class="divFormAds">
<form action="verify.php" method="post">
  <div class="input">
    <label for="newPassword" class="form-label">Verifica Account FB</label><br>
  </div>
  <div class="input">
    <label for="Password" class="form-label">Password</label><br>
    <input type="password" class="input-form" name="pswVerificaAccount" required>
  </div class="input">
  <button type="submit" class="button-form">Verifica Account</button>
</form>
</div>
    
</body>

</html>