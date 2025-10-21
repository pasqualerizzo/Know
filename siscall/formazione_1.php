<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();
/**
 * Inizio Processo prelievo giornaliero Siscall1
 */
$dataImport = date('Y-m-d H:i:s');
$dataControllo = date('Y-m-01', strtotime('-4 months'));
/**
 * Query Primo raggruppamento F1
 */
$queryF1 = "SELECT user,nomeCompleto,giorno,sum(numero),dataAssunzione FROM `stringheSiscall` "
        . "where dataAssunzione>='2023-07-01' and giorno>=dataAssunzione and dataAssunzione>='$dataControllo' group by user,giorno";
$risultatoF1 = $conn19->query($queryF1);
if (!$risultatoF1) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $conn->real_escape_string($conn->error);
} else {
    $conn19->query("TRUNCATE TABLE fsiscall1");

    while ($rigaF1 = $risultatoF1->fetch_array()) {
        $user = $rigaF1[0];
        $nomeCognome = $rigaF1[1];
        $giorno = $rigaF1[2];
        $numero = $rigaF1[3];
        $dataAssunzione = date('Y-m-d', strtotime($rigaF1[4]));
        $queryInserimentoF1 = "INSERT INTO `fsiscall1`(`user`, `nomeCompleto`, `giorno`, `numero`, `dataAssunzione`) "
                . "VALUES ('$user','$nomeCognome','$giorno','$numero','$dataAssunzione')";
        $conn19->query($queryInserimentoF1);
    }
}
/**
 * Query Primo raggruppamento F2
 */
$queryF1 = "SELECT user,nomeCompleto,giorno,sum(numero),dataAssunzione FROM `stringheSiscall2` "
        . "where dataAssunzione>='2023-07-01' and giorno>=dataAssunzione and dataAssunzione>='$dataControllo' group by user,giorno";
$risultatoF1 = $conn19->query($queryF1);
if (!$risultatoF1) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $conn->real_escape_string($conn->error);
} else {
    $conn19->query("TRUNCATE TABLE fsiscall2");

    while ($rigaF1 = $risultatoF1->fetch_array()) {
        $user = $rigaF1[0];
        $nomeCognome = $rigaF1[1];
        $giorno = $rigaF1[2];
        $numero = $rigaF1[3];
        $dataAssunzione = date('Y-m-d', strtotime($rigaF1[4]));
        $queryInserimentoF1 = "INSERT INTO `fsiscall2`(`user`, `nomeCompleto`, `giorno`, `numero`, `dataAssunzione`) "
                . "VALUES ('$user','$nomeCognome','$giorno','$numero','$dataAssunzione')";
        $conn19->query($queryInserimentoF1);
    }
}

/**
 * Query Primo raggruppamento F4
 */
$queryF1 = "SELECT user,nomeCompleto,giorno,sum(numero),dataAssunzione FROM `stringheSiscall4` "
        . "where dataAssunzione>='2023-07-01' and giorno>=dataAssunzione and dataAssunzione>='$dataControllo' group by user,giorno";
$risultatoF1 = $conn19->query($queryF1);
if (!$risultatoF1) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $conn->real_escape_string($conn->error);
} else {
    $conn19->query("TRUNCATE TABLE fsiscall4");

    while ($rigaF1 = $risultatoF1->fetch_array()) {
        $user = $rigaF1[0];
        $nomeCognome = $rigaF1[1];
        $giorno = $rigaF1[2];
        $numero = $rigaF1[3];
        $dataAssunzione = date('Y-m-d', strtotime($rigaF1[4]));
        $queryInserimentoF1 = "INSERT INTO `fsiscall4`(`user`, `nomeCompleto`, `giorno`, `numero`, `dataAssunzione`) "
                . "VALUES ('$user','$nomeCognome','$giorno','$numero','$dataAssunzione')";
        $conn19->query($queryInserimentoF1);
    }
}

/**
 * Query Primo raggruppamento F4TC
 */
$queryF1 = "SELECT user,nomeCompleto,giorno,sum(numero),dataAssunzione FROM `stringheSiscall4TC` "
        . "where dataAssunzione>='2023-07-01' and giorno>=dataAssunzione and dataAssunzione>='$dataControllo' group by user,giorno";
$risultatoF1 = $conn19->query($queryF1);
if (!$risultatoF1) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $conn->real_escape_string($conn->error);
} else {
    $conn19->query("TRUNCATE TABLE fsiscall4TC");

    while ($rigaF1 = $risultatoF1->fetch_array()) {
        $user = $rigaF1[0];
        $nomeCognome = $rigaF1[1];
        $giorno = $rigaF1[2];
        $numero = $rigaF1[3];
        $dataAssunzione = date('Y-m-d', strtotime($rigaF1[4]));
        $queryInserimentoF1 = "INSERT INTO `fsiscall4TC`(`user`, `nomeCompleto`, `giorno`, `numero`, `dataAssunzione`) "
                . "VALUES ('$user','$nomeCognome','$giorno','$numero','$dataAssunzione')";
        $conn19->query($queryInserimentoF1);
    }
}

/**
 * Query Primo raggruppamento FDigital
 */
$queryF1 = "SELECT user,nomeCompleto,giorno,sum(numero),dataAssunzione FROM `stringheSiscallDigital` "
        . "where dataAssunzione>='2023-07-01' and giorno>=dataAssunzione and dataAssunzione>='$dataControllo' group by user,giorno";
$risultatoF1 = $conn19->query($queryF1);
if (!$risultatoF1) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $conn->real_escape_string($conn->error);
} else {
    $conn19->query("TRUNCATE TABLE fsiscallDIGITAL");

    while ($rigaF1 = $risultatoF1->fetch_array()) {
        $user = $rigaF1[0];
        $nomeCognome = $rigaF1[1];
        $giorno = $rigaF1[2];
        $numero = $rigaF1[3];
        $dataAssunzione = date('Y-m-d', strtotime($rigaF1[4]));
        $queryInserimentoF1 = "INSERT INTO `fsiscallDIGITAL`(`user`, `nomeCompleto`, `giorno`, `numero`, `dataAssunzione`) "
                . "VALUES ('$user','$nomeCognome','$giorno','$numero','$dataAssunzione')";
        $conn19->query($queryInserimentoF1);
    }
}

/**
 * Query Primo raggruppamento FGT
 */
$queryF1 = "SELECT user,nomeCompleto,giorno,sum(numero),dataAssunzione FROM `stringheSiscallGT` "
        . "where dataAssunzione>='2023-07-01' and giorno>=dataAssunzione and dataAssunzione>='$dataControllo' group by user,giorno";
$risultatoF1 = $conn19->query($queryF1);
if (!$risultatoF1) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $conn->real_escape_string($conn->error);
} else {
    $conn19->query("TRUNCATE TABLE fsiscallGT");

    while ($rigaF1 = $risultatoF1->fetch_array()) {
        $user = $rigaF1[0];
        $nomeCognome = $rigaF1[1];
        $giorno = $rigaF1[2];
        $numero = $rigaF1[3];
        $dataAssunzione = date('Y-m-d', strtotime($rigaF1[4]));
        $queryInserimentoF1 = "INSERT INTO `fsiscallGT`(`user`, `nomeCompleto`, `giorno`, `numero`, `dataAssunzione`) "
                . "VALUES ('$user','$nomeCognome','$giorno','$numero','$dataAssunzione')";
        $conn19->query($queryInserimentoF1);
    }
}

$queryRicerca = "SELECT user, nomeCompleto,(sum(numero) over (PARTITION BY user order by giorno ))/3600 as ore,dataAssunzione,giorno,numero FROM `fsiscall1` where dataAssunzione>='2023-07-01' and giorno>=dataAssunzione group by user,giorno";
$risultato = $conn19->query($queryRicerca);
if (!$risultato) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $conn->real_escape_string($conn->error);
} else {
    $queryTruncate = "TRUNCATE TABLE `formazione`";
    $conn19->query($queryTruncate);

    while ($riga = $risultato->fetch_array()) {
        $user = $riga[0];
        $nomeCompleto = $riga[1];
        $ore = $riga[2];
        $dataAssunzione = date('Y-m-d', strtotime($riga[3]));
        $provenienza = "siscall";
        $giorno = $riga[4];
        $lavorato = $riga[5] / 3600;

        $queryInserimento = "INSERT INTO `formazione`"
                . "(`user`, `nomeCompleto`, `ore`,dataAssunzione,provenienza,giorno,lavorato) "
                . "VALUES ('$user','$nomeCompleto','$ore','$dataAssunzione','$provenienza','$giorno','$lavorato')";
        $conn19->query($queryInserimento);
    }
}

$queryRicerca = "SELECT user, nomeCompleto,(sum(numero) over (PARTITION BY user order by giorno ))/3600 as ore,dataAssunzione,giorno,numero FROM `fsiscall2` where dataAssunzione>='2023-07-01' and giorno>=dataAssunzione group by user,giorno";
$risultato = $conn19->query($queryRicerca);
if (!$risultato) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $conn->real_escape_string($conn->error);
} else {


    while ($riga = $risultato->fetch_array()) {
        $user = $riga[0];
        $nomeCompleto = $riga[1];
        $ore = $riga[2];
        $dataAssunzione = date('Y-m-d', strtotime($riga[3]));
        $provenienza = "siscall2";
        $giorno = $riga[4];
        $lavorato = $riga[5] / 3600;

        $queryInserimento = "INSERT INTO `formazione`"
                . "(`user`, `nomeCompleto`, `ore`,dataAssunzione,provenienza,giorno,lavorato) "
                . "VALUES ('$user','$nomeCompleto','$ore','$dataAssunzione','$provenienza','$giorno','$lavorato')";
        $conn19->query($queryInserimento);
    }
}

$queryRicerca = "SELECT user, nomeCompleto,(sum(numero) over (PARTITION BY user order by giorno ))/3600 as ore,dataAssunzione,giorno,numero FROM `fsiscall4` where dataAssunzione>='2023-07-01' and giorno>=dataAssunzione group by user,giorno";
$risultato = $conn19->query($queryRicerca);
if (!$risultato) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $conn->real_escape_string($conn->error);
} else {


    while ($riga = $risultato->fetch_array()) {
        $user = $riga[0];
        $nomeCompleto = $riga[1];
        $ore = $riga[2];
        $dataAssunzione = date('Y-m-d', strtotime($riga[3]));
        $provenienza = "siscall4";
        $giorno = $riga[4];
        $lavorato = $riga[5] / 3600;

        $queryInserimento = "INSERT INTO `formazione`"
                . "(`user`, `nomeCompleto`, `ore`,dataAssunzione,provenienza,giorno,lavorato) "
                . "VALUES ('$user','$nomeCompleto','$ore','$dataAssunzione','$provenienza','$giorno','$lavorato')";
        $conn19->query($queryInserimento);
    }
}

$queryRicerca = "SELECT user, nomeCompleto,(sum(numero) over (PARTITION BY user order by giorno ))/3600 as ore,dataAssunzione,giorno,numero FROM `fsiscallDIGITAL` where dataAssunzione>='2023-07-01' and giorno>=dataAssunzione group by user,giorno";
$risultato = $conn19->query($queryRicerca);
if (!$risultato) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $conn->real_escape_string($conn->error);
} else {


    while ($riga = $risultato->fetch_array()) {
        $user = $riga[0];
        $nomeCompleto = $riga[1];
        $ore = $riga[2];
        $dataAssunzione = date('Y-m-d', strtotime($riga[3]));
        $provenienza = "siscallDigital";
        $giorno = $riga[4];
        $lavorato = $riga[5] / 3600;

        $queryInserimento = "INSERT INTO `formazione`"
                . "(`user`, `nomeCompleto`, `ore`,dataAssunzione,provenienza,giorno,lavorato) "
                . "VALUES ('$user','$nomeCompleto','$ore','$dataAssunzione','$provenienza','$giorno',$lavorato)";
        $conn19->query($queryInserimento);
    }
}


$queryRicerca = "SELECT user, nomeCompleto,(sum(numero) over (PARTITION BY user order by giorno ))/3600 as ore,dataAssunzione,giorno,numero FROM `fsiscallGT` where dataAssunzione>='2023-07-01' and giorno>=dataAssunzione group by user,giorno";
$risultato = $conn19->query($queryRicerca);
if (!$risultato) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $conn->real_escape_string($conn->error);
} else {


    while ($riga = $risultato->fetch_array()) {
        $user = $riga[0];
        $nomeCompleto = $riga[1];
        $ore = $riga[2];
        $dataAssunzione = date('Y-m-d', strtotime($riga[3]));
        $provenienza = "siscallGT";
        $giorno = $riga[4];
        $lavorato = $riga[5] / 3600;

        $queryInserimento = "INSERT INTO `formazione`"
                . "(`user`, `nomeCompleto`, `ore`,dataAssunzione,provenienza,giorno,lavorato) "
                . "VALUES ('$user','$nomeCompleto','$ore','$dataAssunzione','$provenienza','$giorno','$lavorato')";
        $conn19->query($queryInserimento);
    }
}

$queryRicerca = "SELECT user, nomeCompleto,(sum(numero) over (PARTITION BY user order by giorno ))/3600 as ore,dataAssunzione,giorno,numero FROM `fsiscall4TC` where dataAssunzione>='2023-07-01' and giorno>=dataAssunzione group by user,giorno";
$risultato = $conn19->query($queryRicerca);
if (!$risultato) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $conn->real_escape_string($conn->error);
} else {


    while ($riga = $risultato->fetch_array()) {
        $user = $riga[0];
        $nomeCompleto = $riga[1];
        $ore = $riga[2];
        $dataAssunzione = date('Y-m-d', strtotime($riga[3]));
        $provenienza = "siscall4TC";
        $giorno = $riga[4];
        $lavorato = $riga[5] / 3600;

        $queryInserimento = "INSERT INTO `formazione`"
                . "(`user`, `nomeCompleto`, `ore`,dataAssunzione,provenienza,giorno) "
                . "VALUES ('$user','$nomeCompleto','$ore','$dataAssunzione','$provenienza','$giorno','$lavorato')";
        $conn19->query($queryInserimento);
    }
}

$queryRicerca = "SELECT user, nomeCompleto,sum(ore) as ore,dataAssunzione,giorno,lavorato FROM `formazione` where dataAssunzione>='2023-07-01' group by user,giorno";
$risultato = $conn19->query($queryRicerca);
if (!$risultato) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $conn->real_escape_string($conn->error);
} else {
    $queryTruncate = "TRUNCATE TABLE `formazioneTotale`";
    $conn19->query($queryTruncate);

    while ($riga = $risultato->fetch_array()) {
        $user = $riga[0];
        $nomeCompleto = $riga[1];
        $ore = $riga[2];
        $dataAssunzione = date('Y-m-d', strtotime($riga[3]));

        $giorno = $riga[4];
        $lavorato = $riga[5];

        $queryInserimento = "INSERT INTO `formazioneTotale`"
                . "(`user`, `nomeCompleto`, `ore`,dataAssunzione,giorno,lavorato) "
                . "VALUES ('$user','$nomeCompleto','$ore','$dataAssunzione','$giorno','$lavorato')";
        $conn19->query($queryInserimento);
    }
}    