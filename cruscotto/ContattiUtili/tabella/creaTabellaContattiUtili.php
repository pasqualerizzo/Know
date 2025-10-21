<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

// Gestione del mese selezionato
$mese_selezionato = isset($_GET['mese']) ? $_GET['mese'] : date('n');
$anno_selezionato = isset($_GET['anno']) ? $_GET['anno'] : date('Y');

// Gestione inserimento/modifica contatti utili
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['data']) && isset($_POST['contattiUtili'])) {
        $data = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_STRING);
        $contattiUtili = filter_input(INPUT_POST, 'contattiUtili', FILTER_SANITIZE_NUMBER_INT);
        
        $query = "UPDATE `contattiUtili` SET `contattiutili` = ? WHERE `data` = ?";
        $stmt = $conn19->prepare($query);
        $stmt->bind_param("is", $contattiUtili, $data);
        $stmt->execute();
    }
}

// Selettore del mese
echo '<form method="get" action="">
        <select name="mese">
            <option value="1"' . ($mese_selezionato == 1 ? ' selected' : '') . '>Gennaio</option>
            <option value="2"' . ($mese_selezionato == 2 ? ' selected' : '') . '>Febbraio</option>
            <option value="3"' . ($mese_selezionato == 3 ? ' selected' : '') . '>Marzo</option>
            <option value="4"' . ($mese_selezionato == 4 ? ' selected' : '') . '>Aprile</option>
            <option value="5"' . ($mese_selezionato == 5 ? ' selected' : '') . '>Maggio</option>
            <option value="6"' . ($mese_selezionato == 6 ? ' selected' : '') . '>Giugno</option>
            <option value="7"' . ($mese_selezionato == 7 ? ' selected' : '') . '>Luglio</option>
            <option value="8"' . ($mese_selezionato == 8 ? ' selected' : '') . '>Agosto</option>
            <option value="9"' . ($mese_selezionato == 9 ? ' selected' : '') . '>Settembre</option>
            <option value="10"' . ($mese_selezionato == 10 ? ' selected' : '') . '>Ottobre</option>
            <option value="11"' . ($mese_selezionato == 11 ? ' selected' : '') . '>Novembre</option>
            <option value="12"' . ($mese_selezionato == 12 ? ' selected' : '') . '>Dicembre</option>
        </select>
        <select name="anno">';
        
for ($anno = 2020; $anno <= 2030; $anno++) {
    echo '<option value="' . $anno . '"' . ($anno_selezionato == $anno ? ' selected' : '') . '>' . $anno . '</option>';
}

echo '</select>
        <button type="submit">Mostra</button>
      </form><br>';

// Query per recuperare i dati del mese selezionato
$queryRicerca = "SELECT * FROM `contattiUtili` 
                 WHERE MONTH(data) = ? AND YEAR(data) = ?
                 ORDER BY data ASC";
$stmt = $conn19->prepare($queryRicerca);
$stmt->bind_param("ii", $mese_selezionato, $anno_selezionato);
$stmt->execute();
$risultato = $stmt->get_result();

// Se la tabella è vuota per il mese selezionato, inserisci tutti i giorni del mese
if ($risultato->num_rows === 0) {
    $numero_giorni = cal_days_in_month(CAL_GREGORIAN, $mese_selezionato, $anno_selezionato);
    
    for ($giorno = 1; $giorno <= $numero_giorni; $giorno++) {
        $data = sprintf('%04d-%02d-%02d', $anno_selezionato, $mese_selezionato, $giorno);
        $nome_giorno = date('l', strtotime($data));
        $giorni_settimana = array(
            'Monday' => 'Lunedì',
            'Tuesday' => 'Martedì',
            'Wednesday' => 'Mercoledì',
            'Thursday' => 'Giovedì',
            'Friday' => 'Venerdì',
            'Saturday' => 'Sabato',
            'Sunday' => 'Domenica'
        );
        $nome_giorno_it = $giorni_settimana[$nome_giorno];
        
        // Verifica se la data esiste già
        $query_check = "SELECT data FROM `contattiUtili` WHERE data = ?";
        $stmt_check = $conn19->prepare($query_check);
        $stmt_check->bind_param("s", $data);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        if ($stmt_check->num_rows === 0) {
            $query = "INSERT INTO `contattiUtili`(`data`, `giorno`, `contattiUtili`) 
                      VALUES (?, ?, 0)";
            $stmt_insert = $conn19->prepare($query);
            $stmt_insert->bind_param("ss", $data, $nome_giorno_it);
            $stmt_insert->execute();
        }
    }
    
    // Riesegui la query dopo l'inserimento
    $stmt = $conn19->prepare($queryRicerca);
    $stmt->bind_param("ii", $mese_selezionato, $anno_selezionato);
    $stmt->execute();
    $risultato = $stmt->get_result();
}

// Mostra la tabella
echo '<table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Data</th>
            <th>Giorno</th>
            <th>Contatti Utili</th>
        </tr>';

while ($riga = $risultato->fetch_assoc()) {
    echo '<tr>
            <td>'.$riga['data'].'</td>
            <td>'.$riga['giorno'].'</td>
            <td>
                <form method="post" action="">
                    <input type="hidden" name="data" value="'.$riga['data'].'">
                    <input type="number" name="contattiUtili" value="'.$riga['contattiUtili'].'" min="0">
                    <button type="submit">Salva</button>
                </form>
            </td>
          </tr>';
}

echo '</table>';