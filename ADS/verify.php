<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

include 'db.php';
include 'log.php';

$psw_errata = "<h1 style='color:red; font-size:30px'>Password Errata</h1>";
$psw_corretta = "<h1 style='color:green; font-size:30px'>Password Corretta</h1>";
$auth_corretta = "<h1 style='color:green; font-size:30px'>Auth Meta Aggiornato</h1>";
$psw_aggiornata = "<h1 style='color:green; font-size:30px'>Password Aggiornata</h1>";
$return_index= "<a href='index.php' style='font-size:25px'>Torna alla pagina principale</a>";
$go_gest_account = "<a href='gestads.html' style='font-size:25px'>Vai alla pagina di modifica account Pubblicitari</a>";
$account_eliminato = "<h1 style='color:green; font-size:30px'>Account Eliminato</h1>";
$account_non = "<h1 style='color:red; font-size:30px'>Account Non Trovato</h1>";
$account_aggiunto = "<h1 style='color:green; font-size:30px'>Account Aggiunto</h1>";

if (isset($_POST['authMeta'])) {
    $sql ="SELECT psw FROM accessi";
    $stmt = $conn->prepare($sql);
    try {
        
        $stmt->execute();
        $publishers = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($publishers) {
            $psw= $publishers["psw"];
            }
    }catch (Exception $e){
        
        throw $e;
        echo $e;
        log_action($e,$conn);
    }
    if ($psw === md5($_POST['psw'])){      
        echo $psw_corretta;
        $aut = $_POST['authMeta'];
        $DateAndTime = date('d-m-Y a', strtotime("+1 month", time()));
        $sql ="UPDATE accessi SET auth_meta='$aut',dataOra='$DateAndTime' WHERE id=1" ;
        $stmt = $conn->prepare($sql);
        try {
            
            $stmt->execute();
            echo $auth_corretta.$return_index;
            log_action("Auth FB Aggiornata!",$conn);
            
        }catch (Exception $e){
            
            throw $e;
            log_action($e,$conn);
            echo $e;
            
        }
        
        
    }else{
        echo $psw_errata.$return_index;
        log_action("Tentativo Modifica Auth FB, Password errata!",$conn);
    } 
    $conn = null;

}elseif (isset($_POST['newPsw'])) {
    $sql ="SELECT psw FROM accessi";
    $stmt = $conn->prepare($sql);
    try {
        
        $stmt->execute();
        $publishers = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($publishers) {
            $psw= $publishers["psw"];
            }
    }catch (Exception $e){
        
        throw $e;
        echo $e;
        log_action($e,$conn);
    }
    if ($psw === md5($_POST['psw'])){ 
        echo $psw_corretta;
        $newPsw = md5($_POST['newPsw']);
        $DateAndTime = date('d-m-Y', time());

        $sql ="UPDATE accessi SET psw='$newPsw' WHERE id=1" ;
        $stmt = $conn->prepare($sql);
        try {
            
            $stmt->execute();
            echo $psw_aggiornata.$return_index;
            log_action("Password Aggiornata!",$conn);
            
            
        }catch (Exception $e){
            
            throw $e;
            echo $e;
            log_action($e,$conn);
            
        }
        $conn = null;


    }else{
        echo $psw_errata.$return_index;
        log_action("Tentativo Modifica Password, Password errata!",$conn);
    } 
    $conn = null;
} 
elseif (isset($_POST['pswVerificaAccount'])) {
    $sql ="SELECT psw FROM accessi";
    $stmt = $conn->prepare($sql);
    try {
        
        $stmt->execute();
        $publishers = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($publishers) {
            $psw= $publishers["psw"];
            }
    }catch (Exception $e){
        
        throw $e;
        echo $e;
        log_action($e,$conn);
    }
    if ($psw === md5($_POST['pswVerificaAccount'])){ 
        $sql ="SELECT account,nome FROM accountPub";
        $stmt = $conn->prepare($sql);
    try {
        
        $stmt->execute();
        $publishers = $stmt->fetchall(PDO::FETCH_ASSOC);
        if ($publishers) {
            echo "<h1 style='padding-top:50px;'>Elenco Account</h1><ul style='font-size:25px;'>";
            foreach ($publishers as $element){
            echo "<li>".$element["nome"]." - ".$element["account"]."</li><br>";

        }
        echo "</ul>".$go_gest_account."<br><br>".$return_index;
        log_action("Account Ads Visualizzati",$conn);
            }
    }catch (Exception $e){
        
        throw $e;
        echo $e;
        log_action($e,$conn);
    }

    }
    $conn = null;
}        

elseif (isset($_POST['accountDelete'])) {
    $sql ="SELECT psw FROM accessi";
    $stmt = $conn->prepare($sql);
    try {
        
        $stmt->execute();
        $publishers = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($publishers) {
            $psw= $publishers["psw"];
            }
    }catch (Exception $e){
        
        throw $e;
        echo $e;
        log_action($e,$conn);
    }
    if ($psw === md5($_POST['psw'])){

        $account_d = $_POST['accountDelete'];
        $sql = "SELECT nome FROM accountPub WHERE nome='$account_d'";
        $stmt = $conn->prepare($sql);
        try {
        
            $stmt->execute();
            $publishers = $stmt->fetchall(PDO::FETCH_ASSOC);
            if ($publishers) {

        
        $sql = "DELETE FROM accountPub WHERE nome='$account_d'";
        $stmt = $conn->prepare($sql);
        try {
        
            $stmt->execute();
            $publishers = $stmt->fetchall(PDO::FETCH_ASSOC);
           
                

            echo $account_eliminato.$go_gest_account."<br><br>".$return_index;
            log_action("Account Ads Eliminato - ".$account_d,$conn);

        
    }catch (Exception $e){
            
            throw $e;
            echo $e;
            log_action($e,$conn);
        }
    } else{
        echo $account_non.$go_gest_account."<br><br>".$return_index;
            log_action("Account Non Trovato - ".$account_d,$conn);
    }
    }catch (Exception $e){
            
        throw $e;
        echo $e;
        log_action($e,$conn);
    }
   
    }
    $conn = null;

}
elseif (isset($_POST['addAccountName'])) {
    $sql ="SELECT psw FROM accessi";
    $stmt = $conn->prepare($sql);
    try {
        
        $stmt->execute();
        $publishers = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($publishers) {
            $psw= $publishers["psw"];
            }
    }catch (Exception $e){
        
        throw $e;
        echo $e;
        log_action($e,$conn);
    }
    if ($psw === md5($_POST['psw'])){
        $nome_AD=$_POST['addAccountName'];
        $num_AD=$_POST['addAccountNu'];

        $sql ="INSERT INTO accountPub(nome, account) VALUES ('$nome_AD','$num_AD')";
        $stmt = $conn->prepare($sql);
        try {
            $stmt->execute();
            
            
            log_action("Account inserito"." - ".$nome_AD,$conn);
            echo $account_aggiunto.$go_gest_account."<br><br>".$return_index;
            log_action("Account Ads aggiunto- ".$nome_AD,$conn);
        }catch (Error $e){
            throw $e;
            
            log_action($e->get_messagge()." - ".$acccountPu["nome"],$conn);
            echo $go_gest_account."<br><br>".$return_index;
        }
        
    
   
    }
    $conn = null;

}
else{
    echo $return_index;
}


