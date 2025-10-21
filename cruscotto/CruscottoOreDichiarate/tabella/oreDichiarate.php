<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

// Gestione parametri
$mese_selezionato = isset($_GET['mese']) ? $_GET['mese'] : date('n');
$anno_selezionato = isset($_GET['anno']) ? $_GET['anno'] : date('Y');
$mandato_selezionato = isset($_GET['mandato']) ? $_GET['mandato'] : 'EnelIn';

// Gestione salvataggio ore
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['data']) && isset($_POST['oreDichiarate']) && isset($_POST['mandato'])) {
    $data = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_STRING);
    $oreDichiarate = filter_input(INPUT_POST, 'oreDichiarate', FILTER_SANITIZE_NUMBER_INT);
    $mandato = filter_input(INPUT_POST, 'mandato', FILTER_SANITIZE_STRING);
    
    // Determina la tabella in base al mandato
    switch($mandato) {
        case 'EnelIn':
            $tabella = 'oreEnelIn';
            break;
        case 'Heracom':
            $tabella = 'oreHeracom';
            break;
        case 'TIMmq':
            $tabella = 'oreTIMmq';
            break;
        default:
            die("Mandato non valido");
    }
    
    $query = "UPDATE `$tabella` SET `oreDichiarate` = ? WHERE `data` = ?";
    $stmt = $conn19->prepare($query);
    $stmt->bind_param("is", $oreDichiarate, $data);
    $stmt->execute();
    
    // Reindirizza per mantenere i parametri GET
    header("Location: ".$_SERVER['PHP_SELF']."?mandato=".$mandato_selezionato."&mese=".$mese_selezionato."&anno=".$anno_selezionato);
    exit;
}

// Determina la tabella in base al mandato selezionato
switch($mandato_selezionato) {
    case 'EnelIn':
        $tabella = 'oreEnelIn';
        break;
    case 'Heracom':
        $tabella = 'oreHeracom';
        break;
    case 'TIMmq':
        $tabella = 'oreTIMmq';
        break;
    default:
        $tabella = 'oreEnelIn';
}

// CORREZIONE: Inserisce i giorni mancanti SOLO se non esistono per il mese/anno specifico
$query_check_mese = "SELECT COUNT(*) as total FROM `$tabella` 
                     WHERE MONTH(data) = ? AND YEAR(data) = ?";
$stmt_check = $conn19->prepare($query_check_mese);
$stmt_check->bind_param("ii", $mese_selezionato, $anno_selezionato);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$row_check = $result_check->fetch_assoc();

// Se non ci sono record per questo mese/anno, inserisci i giorni
if ($row_check['total'] === 0) {
    $numero_giorni = cal_days_in_month(CAL_GREGORIAN, $mese_selezionato, $anno_selezionato);
    $giorni_settimana = array(
        'Monday' => 'Lunedì',
        'Tuesday' => 'Martedì',
        'Wednesday' => 'Mercoledì',
        'Thursday' => 'Giovedì',
        'Friday' => 'Venerdì',
        'Saturday' => 'Sabato',
        'Sunday' => 'Domenica'
    );
    
    for ($giorno = 1; $giorno <= $numero_giorni; $giorno++) {
        $data = sprintf('%04d-%02d-%02d', $anno_selezionato, $mese_selezionato, $giorno);
        $nome_giorno = date('l', strtotime($data));
        $nome_giorno_it = $giorni_settimana[$nome_giorno];
        
        $query_insert = "INSERT INTO `$tabella`(`data`, `giorno`, `oreDichiarate`, `mandato`) 
                      VALUES (?, ?, 0, ?)";
        $stmt_insert = $conn19->prepare($query_insert);
        $stmt_insert->bind_param("sss", $data, $nome_giorno_it, $mandato_selezionato);
        $stmt_insert->execute();
    }
}

// Form di selezione
echo '<form method="get" action="'.htmlspecialchars($_SERVER['PHP_SELF']).'">
        <label for="mandato">Mandato:</label>
        <select name="mandato" id="mandato">
            <option value="EnelIn"'.($mandato_selezionato == 'EnelIn' ? ' selected' : '').'>EnelIn</option>
            <option value="Heracom"'.($mandato_selezionato == 'Heracom' ? ' selected' : '').'>Heracom</option>
            <option value="TIMmq"'.($mandato_selezionato == 'TIMmq' ? ' selected' : '').'>TIMmq</option>
        </select>
        
        <label for="mese">Mese:</label>
        <select name="mese" id="mese">
            <option value="1"'.($mese_selezionato == 1 ? ' selected' : '').'>Gennaio</option>
            <option value="2"'.($mese_selezionato == 2 ? ' selected' : '').'>Febbraio</option>
            <option value="3"'.($mese_selezionato == 3 ? ' selected' : '').'>Marzo</option>
            <option value="4"'.($mese_selezionato == 4 ? ' selected' : '').'>Aprile</option>
            <option value="5"'.($mese_selezionato == 5 ? ' selected' : '').'>Maggio</option>
            <option value="6"'.($mese_selezionato == 6 ? ' selected' : '').'>Giugno</option>
            <option value="7"'.($mese_selezionato == 7 ? ' selected' : '').'>Luglio</option>
            <option value="8"'.($mese_selezionato == 8 ? ' selected' : '').'>Agosto</option>
            <option value="9"'.($mese_selezionato == 9 ? ' selected' : '').'>Settembre</option>
            <option value="10"'.($mese_selezionato == 10 ? ' selected' : '').'>Ottobre</option>
            <option value="11"'.($mese_selezionato == 11 ? ' selected' : '').'>Novembre</option>
            <option value="12"'.($mese_selezionato == 12 ? ' selected' : '').'>Dicembre</option>
        </select>
        
        <label for="anno">Anno:</label>
        <select name="anno" id="anno">';
        
for ($anno = 2020; $anno <= 2030; $anno++) {
    echo '<option value="'.$anno.'"'.($anno_selezionato == $anno ? ' selected' : '').'>'.$anno.'</option>';
}

echo '</select>
        <button type="submit">Mostra</button>
      </form><br>';

// Recupera i dati del mese selezionato
$queryRicerca = "SELECT * FROM `$tabella` 
                 WHERE MONTH(data) = ? AND YEAR(data) = ?
                 ORDER BY data ASC";
$stmt = $conn19->prepare($queryRicerca);
$stmt->bind_param("ii", $mese_selezionato, $anno_selezionato);
$stmt->execute();
$risultato = $stmt->get_result();

// Mostra la tabella con i dati
echo '<table border="1" cellpadding="5" cellspacing="0" style="width:100%">
        <thead>
            <tr>
                <th>Data</th>
                <th>Giorno</th>
                <th>Ore Dichiarate</th>
                <th>Mandato</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>';

$totale_ore = 0;

while ($riga = $risultato->fetch_assoc()) {
    $totale_ore += $riga['oreDichiarate'];
    echo '<tr>
            <td>'.$riga['data'].'</td>
            <td>'.$riga['giorno'].'</td>
            <td>'.$riga['oreDichiarate'].'</td>
            <td>'.$riga['mandato'].'</td>
            <td>
                <form method="post" action="'.htmlspecialchars($_SERVER['PHP_SELF']).'?mandato='.$mandato_selezionato.'&mese='.$mese_selezionato.'&anno='.$anno_selezionato.'" style="display:inline;">
                    <input type="hidden" name="data" value="'.$riga['data'].'">
                    <input type="hidden" name="mandato" value="'.$mandato_selezionato.'">
                    <input type="number" name="oreDichiarate" value="'.$riga['oreDichiarate'].'" min="0" style="width:60px;">
                    <button type="submit" style="padding:2px 5px;">Salva</button>
                </form>
            </td>
          </tr>';
}

echo '</tbody>
        <tfoot>
            <tr>
                <td colspan="2"><strong>Totale Ore</strong></td>
                <td><strong>'.$totale_ore.'</strong></td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
      </table>';

$conn19->close();
?>