<?php
$html .= "<thead>";
$html .= "<tr>";
$html .= "<th>Mandato</th>";
$html .= "<th>Sede</th>";
$html .= "<th>Campagna</th>";

$html .= "<th colspan='2' style='border-left: 2px solid lightslategray' >Prodotto</th>";
$html .= "<th colspan='2' style='border-left: 2px solid lightslategray'>Inserito</th>";
$html .= "<th colspan='2' style='border-left: 2px solid lightslategray'>KO</th>";
$html .= "<th colspan='2' style='border-left: 2px solid lightslategray'>BackLog</th>";
$html .= "<th colspan='2' style='border-left: 2px solid lightslategray'>BackLog Patner</th>";
$html .= "<th style='border-left: 2px solid lightslategray'>Ore</th>";
$html .= "<th colspan='1' style='background-color: yellow;border-left: 2px solid lightslategray'>% Su Inserito</th>";
$html .= "<th colspan='2' style='background-color:mediumseagreen;border-left: 2px solid lightslategray'>Resa su Prodotto</th>";
$html .= "<th colspan='2' style='background-color:mediumseagreen;border-left: 2px solid lightslategray'>Resa su Inserito</th>";

$html .= "<th colspan='3' style='background-color: indianred;border-left: 2px solid lightslategray'>Metodo di Pagamentosu OK<br>Esclusi Subentri</th>";
$html .= "<th colspan='3' style='background-color: #d57676;border-left: 2px solid lightslategray'>Metodo di Pagamento su OK<br>Inclusi Subentri</th>";

$html .= "<th colspan='3' style='background-color: #f65936;border-left: 2px solid lightslategray'>Metodo di Invio</th>";
$html .= "<th colspan='3' style='background-color: #e19d9d;border-left: 2px solid lightslategray'>Metodo di Invio su Ok</th>";

$html .= "<th colspan='4' style='background-color: #289ee1;border-left: 2px solid lightslategray'>Comodity</th>";

$html .= "<th colspan='1' style='background-color: yellow;border-left: 2px solid lightslategray'>% Su Post Ok</th>";

$html .= "<th colspan='2' style='background-color: goldenrod;border-left: 2px solid lightslategray'>Post OK</th>";
$html .= "<th colspan='2' style='background-color: goldenrod;border-left: 2px solid lightslategray'>Post KO</th>";
$html .= "<th colspan='2' style='background-color: goldenrod;border-left: 2px solid lightslategray'>Post BackLog</th>";
$html .= "<th colspan='2' style='background-color: #928BFF;border-left: 2px solid lightslategray'>Tasso Mortalità</th>";
$html .= "<th colspan='5' style='background-color: #928BFF;border-left: 2px solid lightslategray'>Periodo Mortalità</th>";


$html .= "</tr>";
$html .= "<tr>";
$html .= "<th colspan='3'>Da: '$dataMinoreIta' A: '$dataMaggioreIta'</th>";

$html .= "<th style='border-left: 2px solid lightslategray'>PDA</th>";
$html .= "<th>Valore</th>";

$html .= "<th style='border-left: 2px solid lightslategray'>PDA</th>";
$html .= "<th>Valore</th>";

$html .= "<th style='border-left: 2px solid lightslategray'>PDA</th>";
$html .= "<th>Valore</th>";

$html .= "<th style='border-left: 2px solid lightslategray'>PDA</th>";
$html .= "<th>Valore</th>";

$html .= "<th style='border-left: 2px solid lightslategray'>PDA</th>";
$html .= "<th>Valre</th>";

$html .= "<th style='border-left: 2px solid lightslategray'>Ore</th>";

$html .= "<th style='background-color: yellow'>%</th>";

$html .= "<th style='background-color:mediumseagreen;border-left: 2px solid lightslategray'>Su PDA</th>";
$html .= "<th style='background-color:mediumseagreen'>Su Valore</th>";

$html .= "<th style='background-color:mediumseagreen;border-left: 2px solid lightslategray'>Su PDA</th>";
$html .= "<th style='background-color:mediumseagreen'>Su Valore</th>";

$html .= "<th style='background-color: indianred;border-left: 2px solid lightslategray'>Boll.</th>";
$html .= "<th style='background-color: indianred'>RID</th>";
$html .= "<th style='background-color: indianred'>B/B+R %</th>";

$html .= "<th style='background-color: #d57676;border-left: 2px solid lightslategray'>Boll.</th>";
$html .= "<th style='background-color: #d57676'>RID</th>";
$html .= "<th style='background-color: #d57676'>B/B+R %</th>";

$html .= "<th style='background-color: #f65936;border-left: 2px solid lightslategray'>Cartaceo</th>";
$html .= "<th style='background-color: #f65936'>Mail</th>";
$html .= "<th style='background-color: #f65936'>M/M+C %</th>";


$html .= "<th style='background-color: #e19d9d;border-left: 2px solid lightslategray'>Cartaceo</th>";
$html .= "<th style='background-color: #e19d9d'>Mail</th>";
$html .= "<th style='background-color: #e19d9d'>M/M+C %</th>";

$html .= "<th style='background-color: #289ee1;border-left: 2px solid lightslategray'>Luce</th>";
$html .= "<th style='background-color: #289ee1'>Gas</th>";
$html .= "<th style='background-color: #289ee1'>Dual</th>";
$html .= "<th style='background-color: #289ee1'>Consenso</th>";
$html .= "<th style='background-color: yellow'>%</th>";

$html .= "<th style='background-color: goldenrod;border-left: 2px solid lightslategray'>PDA</th>";
$html .= "<th style='background-color: goldenrod'>%</th>";

$html .= "<th style='background-color: goldenrod;border-left: 2px solid lightslategray'>PDA</th>";
$html .= "<th style='background-color: goldenrod'>%</th>";

$html .= "<th style='background-color: goldenrod;border-left: 2px solid lightslategray'>PDA</th>";
$html .= "<th style='background-color: goldenrod'>%</th>";

$html .= "<th style='background-color: #928BFF;border-left: 1px solid lightslategray'>PDA</th>";
$html .= "<th style='background-color: #928BFF;border-left: 1px solid lightslategray'>%</th>";

$html .= "<th style='background-color: #928BFF;border-left: 1px solid lightslategray'>Δ1</th>";
$html .= "<th style='background-color: #928BFF;border-left: 1px solid lightslategray'>Δ3</th>";
$html .= "<th style='background-color: #928BFF;border-left: 1px solid lightslategray'>Δ6</th>";
$html .= "<th style='background-color: #928BFF;border-left: 1px solid lightslategray'>Δ9</th>";
$html .= "<th style='background-color: #928BFF;border-left: 1px solid lightslategray'>Δ-E</th>";

$html .= "</tr>";
$html .= "</thead>";
    
?>