<?php

header('Access-Control-Allow-Origin: *');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscallLead.php";
$obj = new connessioneSiscallLead();
$conn = $obj->apriConnessioneSiscallLead();
$dataOdierna = date('Y-m-d');

$ricerca = filter_input(INPUT_POST, 'lista', FILTER_SANITIZE_STRING);
$riga = "";
$totale = [];

$query = "SELECT status,COUNT(`status`) FROM vicidial_list WHERE list_id='4042'    GROUP BY status";

$risultato = $conn->query($query);
$conteggio = $risultato->num_rows;

if ($conteggio == 0) {
    $riga = "Nessun Risultato!!!";
    //echo $riga;
    $p=[];
    $p["NEW"]="0";
    array_push($totale, $p);
} else {
    $a = [];
    while ($lista = $risultato->fetch_array()) {
        $riga = [
            $lista[0] => $lista[1],
        ];
        $a = array_merge($a, $riga);
    }
    array_push($totale, $a);
}
$query = "SELECT status, COUNT(`status`) 
FROM vicidial_list 
WHERE list_id IN ('1005', '1006', '1017', '1018') 
GROUP BY status;
";
//echo $query ;
$risultato = $conn->query($query);
$conteggio = $risultato->num_rows;

if ($conteggio == 0) {
    $riga = "Nessun Risultato!!!";
    //echo $riga;
     $p=[];
   $p["NEW"]="0";
    array_push($totale, $p);
} else {
    $b = [];
    while ($lista = $risultato->fetch_array()) {
        $riga = [
            $lista[0] => $lista[1],
        ];
        $b = array_merge($b, $riga);
    }
    array_push($totale, $b);
}

$query = "SELECT status, COUNT(`status`) 
FROM vicidial_list 
WHERE list_id IN ('11118', '11119') 
GROUP BY status;
";
//a
$risultato = $conn->query($query);
$conteggio = $risultato->num_rows;

if ($conteggio == 0) {
    $riga = "Nessun Risultato!!!";
    //echo $riga;
     $p=[];
    $p["NEW"]="0";
    array_push($totale, $p);
} else {
    $c = [];
    while ($lista = $risultato->fetch_array()) {
        $riga = [
            $lista[0] => $lista[1],
        ];
        $c = array_merge($c, $riga);
    }
    array_push($totale, $c);
}
$query = "SELECT status, COUNT(`status`) 
FROM vicidial_list 
WHERE list_id IN ('2028', '2029', '2030', '2031', '2032', '2033', '2034', '2035', '2036', '2037', '2038', '2039', '2040', '2041', '2042', '2043','2046','2047','2000') 
GROUP BY status;
";

$risultato = $conn->query($query);
$conteggio = $risultato->num_rows;

if ($conteggio == 0) {
    $riga = "Nessun Risultato!!!";
   //echo $riga;
     $p=[];
    $p["NEW"]="0";
    array_push($totale, $p);
} else {
    $d = [];
    while ($lista = $risultato->fetch_array()) {
        $riga = [
            $lista[0] => $lista[1],
        ];
        $d = array_merge($d, $riga);
    }
    array_push($totale, $d);
}
$query = "SELECT status, COUNT(`status`) 
FROM vicidial_list 
WHERE list_id='2097' 
GROUP BY status
";

$risultato = $conn->query($query);
$conteggio = $risultato->num_rows;

if ($conteggio == 0) {
    $riga = "Nessun Risultato!!!";
    //echo $riga;
     $p=[];
    $p["NEW"]="0";
    array_push($totale, $p);
} else {
    $d = [];
    while ($lista = $risultato->fetch_array()) {
        $riga = [
            $lista[0] => $lista[1],
        ];
        $d = array_merge($d, $riga);
    }
    array_push($totale, $d);
}

/**
 * Inserimento liste 200 parte telco
 */

$query = "SELECT status, COUNT(`status`) 
FROM vicidial_list 
WHERE list_id IN ('2044', '2048', '2049','2024') 
GROUP BY status
";

$risultato = $conn->query($query);
$conteggio = $risultato->num_rows;

if ($conteggio == 0) {
    $riga = "Nessun Risultato!!!";
    //echo $riga;
     $p=[];
   $p["NEW"]="0";
    array_push($totale, $p);
} else {
    $d = [];
    while ($lista = $risultato->fetch_array()) {
        $riga = [
            $lista[0] => $lista[1],
        ];
        $d = array_merge($d, $riga);
    }
    array_push($totale, $d);
}
/**
 *  Calcolo dei new per la lista 2099
 */
$query = "SELECT status, COUNT(`status`) 
FROM vicidial_list 
WHERE list_id in ('2097') 
GROUP BY status
";

$risultato = $conn->query($query);
$conteggio = $risultato->num_rows;

if ($conteggio == 0) {
    $riga = "Nessun Risultato!!!";
    //echo $riga;
     $p=[];
    $p["NEW"]="0";
    array_push($totale, $p);
} else {
    $d = [];
    while ($lista = $risultato->fetch_array()) {
        $riga = [
            $lista[0] => $lista[1],
        ];
        $d = array_merge($d, $riga);
    }
    array_push($totale, $d);
}

/**
 *  Calcolo dei new per la lista metaform
 */
$query = "SELECT status, COUNT(`status`) 
FROM vicidial_list 
WHERE list_id='401' 
GROUP BY status
";

$risultato = $conn->query($query);
$conteggio = $risultato->num_rows;

if ($conteggio == 0) {
    $riga = "Nessun Risultato!!!";
    //echo $riga;
     $p=[];
    $p["NEW"]="0";
    array_push($totale, $p);
} else {
    $d = [];
    while ($lista = $risultato->fetch_array()) {
        $riga = [
            $lista[0] => $lista[1],
        ];
        $d = array_merge($d, $riga);
    }
    array_push($totale, $d);
}

/**
 *  Calcolo dei new per la lista 2599
 */
$query = "SELECT status, COUNT(`status`) 
FROM vicidial_list 
WHERE list_id in ('2599') 
GROUP BY status
";

$risultato = $conn->query($query);
$conteggio = $risultato->num_rows;

if ($conteggio == 0) {
    $riga = "Nessun Risultato!!!";
    //echo $riga;
     $p=[];
    $p["NEW"]="0";
    array_push($totale, $p);
} else {
    $d = [];
    while ($lista = $risultato->fetch_array()) {
        $riga = [
            $lista[0] => $lista[1],
        ];
        $d = array_merge($d, $riga);
    }
    array_push($totale, $d);
}


echo json_encode($totale);

