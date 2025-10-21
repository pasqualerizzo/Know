<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

// Verifica che il mese sia stato passato correttamente
$mese = isset($_POST["mese"]) ? trim($_POST["mese"]) : date('Y-m');
if (!preg_match('/^\d{4}-\d{2}$/', $mese)) {
    die("Formato mese non valido. Usare YYYY-MM");
}



$contattiUtiliTotale = 0;
$pezzolordoSede  =0; 
$dataMinore = $mese . "-01";
$dataMaggiore = date('Y-m-t', strtotime($dataMinore)); // 't' restituisce l'ultimo giorno del mese
$contattiUtiliSede  = 0;
// Debug: verifica le date generate
error_log("Date range: $dataMinore - $dataMaggiore");

$html = "<table class='blueTable'>";
include "../../tabella/intestazioneTabellaCu.php";

// Query corretta con parametri sicuri
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

// Usa prepared statements per evitare SQL injection
$stmt = $conn19->prepare($queryCrm);
if (!$stmt) {
    die("Errore preparazione query: " . $conn19->error);
}

$stmt->bind_param("ssss", $dataMinore, $dataMaggiore, $dataMinore, $dataMaggiore);
$stmt->execute();
$risultatoCrm = $stmt->get_result();

if ($risultatoCrm) {
    if ($risultatoCrm->num_rows > 0) {
        // Intestazione colonne aggiuntive
//        $html .= "<tr>
//                    <th>Data</th>
//                    <th>Giorno</th>
//                    <th>Contatti Utili</th>
//                    <th>Pezzo Lordo</th>
//                    <th>Convertito</th>
//   
//                 </tr>";
        
        while ($rigaCRM = $risultatoCrm->fetch_array()) {
            $data = $rigaCRM["data"];
            $giorno = $rigaCRM["giorno_settimana"];
            $contattiUtili = $rigaCRM["contattiutili"];
            $pezzolordo = $rigaCRM["pezzo_lordo_totale"];
            
            // Calcolo convertito con gestione divisione per zero
          $convertito = ($pezzolordo > 0 && $contattiUtili > 0) ? $contattiUtili / $pezzolordo : 0;
//$percentuale = $convertito * 100;
//$percentuale_formattata = number_format($percentuale, 2) . '%';
            
            // Calcolo percentuale conversione
//            $percentuale = ($pezzolordo != 0) ? (  $pezzolordo / $contattiUtili) * 100 : 0;
//            $percentualeFormattata = number_format($percentuale, 2) . '%';
            
            $html .= "<tr>";
            $html .= "<td>".htmlspecialchars($data)."</td>";
            $html .= "<td>".htmlspecialchars($giorno)."</td>";
            $html .= "<td>".htmlspecialchars($contattiUtili)."</td>";
            $html .= "<td>".htmlspecialchars($pezzolordo)."</td>";
            $html .= "<td>".htmlspecialchars(number_format($convertito, 2))."</td>";
//            $html .= "<td>".htmlspecialchars($percentualeFormattata)."</td>";
            $html .= "</tr>";

            $contattiUtiliSede += $contattiUtili;
            $pezzolordoSede += $pezzolordo;
        }

        $contattiUtiliTotale = $contattiUtiliSede;
        $pezzolordoTotale = $pezzolordoSede;
        
        // Calcolo totali con gestione divisione per zero
        $convertitototale = ($pezzolordoTotale != 0) ?   $pezzolordoTotale / $contattiUtiliTotale: 0;
        $percentualeTotale = ($pezzolordoTotale != 0) ? ($pezzolordoTotale / $contattiUtiliTotale  ) * 100 : 0;
        $percentualeTotaleFormattata = number_format($percentualeTotale, 2) . '%';
        
        $html .= "<tr style='background-color: orangered;border: 2px solid lightslategray'>";
        $html .= "<td colspan='2'>TOTALE</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>".htmlspecialchars($contattiUtiliTotale)."</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>".htmlspecialchars($pezzolordoTotale)."</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>".htmlspecialchars(number_format($convertitototale, 2))."</td>";
//        $html .= "<td style='border-left: 2px solid lightslategray'>".htmlspecialchars($percentualeTotaleFormattata)."</td>";
        $html .= "</tr>";
    } else {
        $html .= "<tr><td colspan='6'>Nessun dato trovato per il mese selezionato</td></tr>";
    }
} else {
    $html .= "<tr><td colspan='6'>Errore nel recupero dei dati: ".htmlspecialchars($conn19->error)."</td></tr>";
}

$html .= "</table>";
echo $html;
?>