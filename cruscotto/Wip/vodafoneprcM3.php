<?php

// Sommario contratti in stato ACQUISITO ed OK FIRMA quotidiani
include_once 'connessioni.php';

$db_instance = new mysqli("crm2.novaholding.it", "bruno", "expqfEMlZ1Rzhzx0", "c1vtiger");

if ($db_instance->connect_error) {
    die("Connection failed: " . $db_instance->connect_error);
}

// Query per ottenere il numero di contratti "OK DEFINITIVO" del mese corrente
$query1 = "
    SELECT COUNT(vodafoneid) AS lavorati
    FROM `vtiger_vodafonecf`
    WHERE DATE_FORMAT(cf_2986, '%Y-%m') = DATE_FORMAT(DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH), '%Y-%m')
    AND cf_3146 = 'OK DEFINITIVO'
";
$result1 = $db_instance->query($query1);
$count1 = ($result1->num_rows > 0) ? $result1->fetch_assoc()['lavorati'] : 0;

// Query per ottenere il numero di contratti in "Wip" del mese corrente
$query2 = "
    SELECT COUNT(vodafoneid) AS wip
    FROM `vtiger_vodafonecf`
    WHERE DATE_FORMAT(cf_2986, '%Y-%m') = DATE_FORMAT(DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH), '%Y-%m')
    AND cf_4134 = 'Wip'
";
$result2 = $db_instance->query($query2);
$count2 = ($result2->num_rows > 0) ? $result2->fetch_assoc()['wip'] : 0;

// Calcolare la percentuale
$totale = $count1 + $count2;
$percentuale = ($totale != 0) ? ($count2 * 100) / $totale : 0;
$percentualeArrotondata = round($percentuale, 2);

// Costruzione della tabella HTML
$tabella = "<table border='2'>
    <tr>
        <td colspan='2' style='background-color: red; color: white; width: 100%; height: 100%; font-size: 30px'>Percentuale Wip Mese Corrente</td>
    </tr>
    <tr>
                <td style='width: 100%; height: 100%; font-size: 30px'>$percentualeArrotondata %</td>
    </tr>
</table>";

print_r($tabella);

?>