<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrm.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$objCrm = new ConnessioneCrm();
$connCrm = $objCrm->apriConnessioneCrm();

session_start();

$username = filter_input(INPUT_POST, "username");
$password = filter_input(INPUT_POST, "password");
$ip = filter_input(INPUT_POST, "ip");

$queryLogin = " select"
        . " user_password, rolename "
        . " from vtiger_users AS utente "
        . " INNER JOIN vtiger_user2role AS ruolo ON utente.id=ruolo.userid "
        . " INNER JOIN vtiger_role AS regole ON ruolo.roleid=regole.roleid "
        . " where user_name='$username' and status='Active'";
$risultato = $connCrm->query($queryLogin);
if ($dato = ($risultato->fetch_array())) {
    if (password_verify($password, $dato[0])) {
        $roleName = $dato[1];
        /**
         * Impostazione della sede in un array
         */
        if (strpos($roleName, "Lamezia") !== false) {
            $sede = "Lamezia";
        } elseif (strpos($roleName, "San Pietro") !== false) {
            $sede = "SanPietro";
        } elseif (strpos($roleName, "San Marco") !== false) {
            $sede = "SanMarco";
        } elseif (strpos($roleName, "Corigliano") !== false) {
            $sede = "Corigliano";
        } elseif (strpos($roleName, "Rende") !== false) {
            $sede = "Rende";
        } elseif (strpos($roleName, "Rende 2") !== false) {
            $sede = "Rende 2";
        } elseif (strpos($roleName, "Castrovillari") !== false) {
            $sede = "Castrovillari";
        } elseif (strpos($roleName, "Vibo") !== false) {
            $sede = "Vibo";
        } elseif (strpos($roleName, "Catanzaro") !== false) {
            $sede = "Catanzaro";
        }
        /**
         * Impostazione dei Permessi in un array
         */
        if (strpos($roleName, "Vivigas") !== false) {
            array_push($permessi, "Vivigas");
        }

        if (strpos($roleName, "Plenitude") !== false) {
            array_push($permessi, "Plenitude");
        }

        if (strpos($roleName, "EnelOut") !== false) {
            array_push($permessi, "EnelOut");
        }

        if (strpos($roleName, "Iren") !== false) {
            array_push($permessi, "Iren");
        }
        /**
         * Impostazione del grado di visualizzazione
         */
        if (strpos($roleName, "CEO") !== false) {
            $visualizzazione = "CEO";
        }
        if (strpos($roleName, "Supervisor") !== false) {
            $visualizzazione = "Supervisor";
        }
        if (strpos($roleName, "Project Manager") !== false) {
            $visualizzazione = "Supervisor";
        }
        
        
        
        
        if (strpos($roleName, "TL") !== false) {
            $visualizzazione = "TL";
        }
        if (strpos($roleName, "HR") !== false) {
            $visualizzazione = "HR";
        }
        if (strpos($roleName, "BO") !== false) {
            $visualizzazione = "BO";
        }
         if (strpos($roleName, "Backoffice") !== false) {
            $visualizzazione = "BO";
        }
        if (strpos($roleName, "Operatore") !== false) {
            $visualizzazione = "Operatore";
        }
        if (strpos($roleName, "Store") !== false) {
            $visualizzazione = "Store";
        }
        if (strpos($roleName, "Marketing") !== false) {
            $visualizzazione = "Marketing";
        }



        $_SESSION["livello"] = $dato[1];
        $_SESSION["username"] = $username;
        $_SESSION["login"] = true;
        $_SESSION["ip"] = $ip;
        $_SESSION["visualizzazione"]=$visualizzazione;
        $_SESSION["sede"]=$sede;
        $_SESSION["permessi"]=$permessi;
        
        header("location:../pannello.php");
    } else {
        $_SESSION["ip"] = $ip;
        $_SESSION["login"] = false;
        header("location:../index.php?errore=password");
    }
} else {
    echo 'Username errata';
}   
