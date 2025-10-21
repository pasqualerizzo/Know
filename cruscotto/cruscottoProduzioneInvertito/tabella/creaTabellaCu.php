<?php
function generaTabellaCu($conn19, $dataMinore, $dataMaggiore) {

$contattiUtiliSede = 0;
$contattiUtiliTotale = 0;
$pezzolordoSede = 0;
$pezzolordoTotale = 0;

$html = "<table class='blueTable'>";
include "../../tabella/intestazioneTabellaCu.php";

$queryCrm = "SELECT
    c.`data` AS 'data',
    c.`giorno` AS 'giorno_settimana',
    c.`contattiUtili` AS 'contattiutili',
    COALESCE(SUM(vh.`pezzoLordo`), 0) AS 'pezzo_lordo_totale'
FROM
    `contattiUtili` c
    LEFT JOIN (
        SELECT 
            h.`data`,
            ah.`pezzoLordo`
        FROM 
            `heracom` h
        JOIN 
            `aggiuntaHeracom` ah ON h.`id` = ah.`id`
        WHERE 
            h.`statoPda` NOT IN ('bozza', 'pratica doppia', 'In attesa Sblocco')
            AND h.`comodity` <> 'Consenso'
            AND h.`data` BETWEEN ? AND ?
    ) AS vh ON c.`data` = vh.`data`
WHERE
    c.`data` BETWEEN ? AND ?
GROUP BY
    c.`data`,
    c.`giorno`,
    c.`contattiUtili`
ORDER BY
    c.`data` ASC";

$stmt = $conn19->prepare($queryCrm);
if (!$stmt) {
    die("Errore preparazione query: " . $conn19->error);
}

$stmt->bind_param("ssss", $dataMinore, $dataMaggiore, $dataMinore, $dataMaggiore);
$stmt->execute();
$risultatoCrm = $stmt->get_result();

if ($risultatoCrm) {
    if ($risultatoCrm->num_rows > 0) {
        while ($rigaCRM = $risultatoCrm->fetch_array()) {
            $data = $rigaCRM["data"];
            $giorno = $rigaCRM["giorno_settimana"];
            $contattiUtili = $rigaCRM["contattiutili"];
            $pezzolordo = $rigaCRM["pezzo_lordo_totale"];
            
            // Calcolo convertito con gestione divisione per zero robusta
            $convertito = ($pezzolordo > 0 && $contattiUtili > 0) ? $pezzolordo / $contattiUtili : 0;
            
            $html .= "<tr>";
            $html .= "<td>".htmlspecialchars($data)."</td>";
            $html .= "<td>".htmlspecialchars($giorno)."</td>";
            $html .= "<td>".htmlspecialchars($contattiUtili)."</td>";
            $html .= "<td>".htmlspecialchars($pezzolordo)."</td>";
            $html .= "<td>".htmlspecialchars(number_format($convertito, 2))."</td>";
            $html .= "</tr>";

            $contattiUtiliSede += $contattiUtili;
            $pezzolordoSede += $pezzolordo;
        }

        $contattiUtiliTotale = $contattiUtiliSede;
        $pezzolordoTotale = $pezzolordoSede;
        
        // Calcolo totali con gestione divisione per zero robusta
        $convertitototale = ($contattiUtiliTotale > 0) ? $pezzolordoTotale / $contattiUtiliTotale : 0;
        $percentualeTotale = ($contattiUtiliTotale > 0) ? ($pezzolordoTotale / $contattiUtiliTotale) * 100 : 0;
        $percentualeTotaleFormattata = number_format($percentualeTotale, 2) . '%';
        
        $html .= "<tr style='background-color: orangered;border: 2px solid lightslategray'>";
        $html .= "<td colspan='2'>TOTALE</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>".htmlspecialchars($contattiUtiliTotale)."</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>".htmlspecialchars($pezzolordoTotale)."</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>".htmlspecialchars(number_format($convertitototale, 2))."</td>";
        $html .= "</tr>";
    } else {
        $html .= "<tr><td colspan='5'>Nessun dato trovato per il mese selezionato</td></tr>";
    }
} else {
    $html .= "<tr><td colspan='5'>Errore nel recupero dei dati: ".htmlspecialchars($conn19->error)."</td></tr>";
}

$html .= "</table>";

    return $html;
}
?>