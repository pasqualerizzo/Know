<?php
// Connessione al database
$mysqli = new mysqli("siscalllead.novadirect.it", "brunoleadmaster", "7bSTSCub[6G*U1x[", "asterisk");

if ($mysqli->connect_error) {
    die("Connessione fallita: " . $mysqli->connect_error);
}

// Disabilita ONLY_FULL_GROUP_BY per compatibilità con query legacy
$mysqli->query("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

$utente = 'fbitonti';
$data = '2025-09-24';

// 1. Recupera tutti gli eventi di LOGIN e LOGOUT ordinati
$queryEventi = "
    SELECT event, event_date
    FROM vicidial_user_log
    WHERE user = ?
      AND DATE(event_date) = ?
      AND event IN ('LOGIN', 'LOGOUT')
    ORDER BY event_date ASC
";

$stmt = $mysqli->prepare($queryEventi);
$stmt->bind_param("ss", $utente, $data);
$stmt->execute();
$result = $stmt->get_result();

$sessions = [];
$loginTime = null;

// 2. Accoppia ogni LOGIN con il LOGOUT successivo
while ($row = $result->fetch_assoc()) {
    if ($row['event'] === 'LOGIN') {
        $loginTime = $row['event_date'];
    } elseif ($row['event'] === 'LOGOUT' && $loginTime !== null) {
        $sessions[] = [
            'login' => $loginTime,
            'logout' => $row['event_date']
        ];
        $loginTime = null;
    }
}

$stmt->close();

// 3. Analizza ogni sessione
foreach ($sessions as $session) {
    $loginTime = $session['login'];
    $logoutTime = $session['logout'];
    $durataSessione = strtotime($logoutTime) - strtotime($loginTime);

    // 4. Somma attività tra login e logout
    $queryAttivita = "
        SELECT 
            SUM(pause_sec + wait_sec + talk_sec + dispo_sec + dead_sec) AS attivita
        FROM vicidial_agent_log
        WHERE user = ?
          AND event_time BETWEEN ? AND ?
    ";

    $stmt2 = $mysqli->prepare($queryAttivita);
    $stmt2->bind_param("sss", $utente, $loginTime, $logoutTime);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $attivita = $result2->fetch_assoc()['attivita'] ?? 0;
    $stmt2->close();

    $differenza = $attivita - $durataSessione;

    // 5. Stampa risultato
    echo "<strong>Sessione:</strong> $loginTime → $logoutTime<br>";
    echo "Durata sessione: $durataSessione sec<br>";
    echo "Attività registrata: $attivita sec<br>";
    echo "Differenza: $differenza sec<br><hr>";
}

$mysqli->close();

?>