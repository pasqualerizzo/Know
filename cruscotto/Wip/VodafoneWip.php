<?php

//sommario contratti in stato ACQUISITO ed OK FIRMA quotidiani

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$query1 = "SELECT"
        . "     COUNT(vodafoneid) AS AVANZAMENTO,"
        . "     cf_4134 AS WIP_DA_CHIAMARE "
        . " FROM "
        . " `vtiger_vodafonecf`  "
        . " WHERE     "
        . " cf_2986 >= DATE_FORMAT(NOW() ,'%Y-%m-01') - INTERVAL 5 MONTH   "
        . " AND cf_2986 < DATE_FORMAT(NOW() ,'%Y-%m-01') + INTERVAL 5 MONTH   "
        . " AND cf_4134 = 'Wip' "
        . " GROUP BY    "
        . " cf_3146 "
        . " LIMIT   "
        . "  1000";

//ECHO $query1;

//$query1= 'SELECT * FROM `vtiger_plenitudecf` WHERE cf_3563 = CURRENT_DATE and cf_3673 = "Ok Firma"';




$result = $conn19->query($query1);

//echo $query1;



$tabella = "<table  border=2>
                <tr><td colspan='2' style='background-color: red; color: white;  width: 100%; height: 100%; font-size: 30px'>MANDATO VODAFONE INSERITI</td></tr>
                <tr><td style='width: 100%; height: 100%; font-size: 30px'>STATO PDA</td><td style='width: 100%; height: 100%; font-size: 30px'>NUMERO</td></tr>";

//fwrite($myfile, $intestazione);      
// fwrite($myfile, $rigcsv);



while ($riga = $result->fetch_assoc()) {
    $tabella .= "<tr><td style='width: 100%; height: 100%; font-size: 30px'>" . $riga['STATO_PDA'] . "</td><td style='width: 100%; height: 100%; font-size: 30px'>" . $riga['AVANZAMENTO'] . "</td></tr>";
    //$rigcsv=$riga['status'].";".$riga['campaign_id'].";".$riga['modify_date'].";".$riga['phone_number'].";".$riga['first_name'].";".$riga['last_name'].";".$riga['address1'].";".$riga['address2'].";".$riga['address3'].";".$riga['email'].";".$riga['comments']."\n";
    //fwrite($myfile, $rigcsv);
}

$tabella .= "</table>";

print_r($tabella);

