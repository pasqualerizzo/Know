<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

// Gestione inserimento/modifica contatti utili
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && isset($_POST['contattiUtili'])) {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $contattiUtili = filter_input(INPUT_POST, 'contattiUtili', FILTER_SANITIZE_NUMBER_INT);
        
        $query = "UPDATE `contattiUtili` SET `contattiutili` = ? WHERE `id` = ?";
        $stmt = $conn19->prepare($query);
        $stmt->bind_param("ii", $contattiUtili, $id);
        $stmt->execute();
    }
}

// Recupera i dati dalla tabella
$queryRicerca = "SELECT * FROM `contattiUtili` ORDER BY data DESC";
$risultato = $conn19->query($queryRicerca);

// Se la tabella è vuota, inserisci la data odierna
if ($risultato->num_rows === 0) {
    $data = date('Y-m-d');
    $giorno = date('l', strtotime($data)); // Restituisce il nome del giorno in inglese
    $query = "INSERT INTO `contattiUtili`(`data`, `giorno`, `contattiutili`) VALUES ('$data','$giorno', 0)";
    $conn19->query($query);
    $risultato = $conn19->query($queryRicerca); // Riesegui la query
}

// Mostra la tabella
echo '<table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Data</th>
            <th>Giorno</th>
            <th>Contatti Utili</th>
            <th>Azioni</th>
        </tr>';

while ($riga = $risultato->fetch_assoc()) {
    echo '<tr>
            <td>'.$riga['id'].'</td>
            <td>'.$riga['data'].'</td>
            <td>'.$riga['giorno'].'</td>
            <td>
                <form method="post" action="">
                    <input type="hidden" name="id" value="'.$riga['id'].'">
                    <input type="number" name="contattiUtili" value="'.$riga['contattiutili'].'" min="0">
                    <button type="submit">Salva</button>
                </form>
            </td>
            <td><button onclick="aggiungiNuovaRiga(\''.$riga['data'].'\')">Aggiungi nuova riga</button></td>
          </tr>';
}

echo '</table>';


?>