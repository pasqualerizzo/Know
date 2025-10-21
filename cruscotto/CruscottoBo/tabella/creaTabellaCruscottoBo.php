<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$dataMinore = filter_input(INPUT_POST, "dataMinore");
$dataMaggiore = filter_input(INPUT_POST, "dataMaggiore");

$dataMinore = date('Y-m-d 00:00:00', strtotime($dataMinore));
$dataMaggiore = date('Y-m-d 23:59:59', strtotime($dataMaggiore));

$oreTotale = 0;
$esaTotale = 0;
$dataMinoreOre = date('Y-m-d', strtotime($dataMinore));
$dataMaggioreOre = date('Y-m-d', strtotime($dataMaggiore));


$totaleOperatore = 0;
$modulo = 0;
$bklTot = 0;
$ko = 0;
$koSede = 0;
$dataOggi = date("Y-m-d");

$op = 0;

$operatore = 0;
$totale = 0;


$pezziPlenitude = 0;
$pezziViviGas = 0;
$elencoMandati = json_decode($_POST["mandato"], true);

$dataMinoreIta = date('Y-m-d 00:00:00', strtotime($dataMinore));
$dataMaggioreIta = date('Y-m-d 23:59:59', strtotime($dataMaggiore));
$arrayVivigas = 0;
$arrayPlenitude = 0;
$arrayGreen = 0;
$arrayUnion = 0;
$arrayIren = 0;
$arrayVoda = 0;

$queryMandato = "";
$lunghezza = count($elencoMandati);

$elencoSede = 0;

$operatori = [
    'Alessandra Viola',
    'Caterina Di Fazio',
    'Francesco Ascone',
    'Martina Branca',
    'giuseppe macri',
    'valeria guadagnuolo',
    'Giorgia Barrese',
    'Gabriella Isabella',
    'Serena Ielapi',
    'Francesco Ieraci'
];

/*
 * Plenitude
 */
$html = "<table class='blueTable'>";
include "../../tabella/intestazioneTabellaControlloBo.php";
/**
 */
$arrayPlenitude = [];
$queryCrmSede = "SELECT
    `nomeOperatore` AS operatore,
    `modulo` AS mandato,
    SUM(
        CASE  WHEN `Stato` = 'Ok Firma'  AND `idPratica` like 'PLE%'   THEN 1 ELSE 0
    END
) AS OK,
SUM(
    CASE WHEN `Stato` IN(
        'In Attesa Firma',
        'DA FIRMARE'
    ) AND `idPratica` like 'PLE%'    THEN 1 ELSE 0
END
) AS BKL,
SUM(
    CASE WHEN `Stato` IN(
        'Ko Recall',
        'Ko Vocal',
        'Ko definitivo',
        'Ko Controllo dati'
  
    )  AND `idPratica` like 'PLE%'   THEN 1 ELSE 0
END
) AS KO

FROM
    `controlloBo`
WHERE
    `nomeOperatore` IN(
        'Alessandra Viola',
        'Caterina Di Fazio',
        'Francesco Ascone',
        'Martina Branca',
        'giuseppe macri',
        
        'Giorgia Barrese',
        'Gabriella Isabella',
        'Serena Ielapi',
        'valeria guadagnuolo ',
        'Francesco Ieraci'
    )AND `idPratica` like 'PLE%'   
    AND `dataOperazione` >= '$dataMinore' 
    AND `dataOperazione` <= '$dataMaggiore'
GROUP BY
    `nomeOperatore`,
    `modulo`
ORDER BY
    `nomeOperatore`,
    `modulo`";

//echo $queryCrmSede;
$risultatoCrmSede = $conn19->query($queryCrmSede);
while ($rigaCRM = $risultatoCrmSede->fetch_array()) {
    $operatore = $rigaCRM["operatore"];
    $idMandato = $rigaCRM["mandato"];
    $ok = $rigaCRM["OK"];
    $bkl = $rigaCRM["BKL"];
    $ko = $rigaCRM["KO"];
       $arrayPlenitude[$operatore] = [
        'ok' => $ok,
        'bkl' => $bkl,
        'ko' => $ko,
    ];
}

$arrayVivigas = [];
$queryCrmSede = "SELECT
    `nomeOperatore` AS operatore,
    `modulo` AS mandato,
    SUM(
        CASE WHEN `Stato` = 'Ok Definitivo' AND `modulo` like 'Vivi' THEN 1 ELSE 0
    END
) AS OK,
SUM(
    CASE WHEN `Stato` IN(
        'Ok Recall',
        'Ok Vocal',
        'Ok inserito'
    ) AND `idPratica` like 'NOV%'   THEN 1 ELSE 0
END
) AS BKL,
SUM(
    CASE WHEN `Stato` IN(
        'Ko Recall',
        'Ko Vocal',
        'Ko definitivo',
        'Ko Controllo Dati'
  
    ) AND `idPratica` like 'NOV%'   THEN 1 ELSE 0
END
) AS KO
FROM
    `controlloBo`
WHERE
    `nomeOperatore` IN(
        'Alessandra Viola',
        'Caterina Di Fazio',
        'Francesco Ascone',
        'Martina Branca',
        'giuseppe macri',
        'valeria guadagnuolo',
        'Giorgia Barrese',
        'Gabriella Isabella',
        'Serena Ielapi',
        'Francesco Ieraci'
    ) AND `idPratica` like 'NOV%'     
    AND `dataOperazione` >= '$dataMinore' 
    AND `dataOperazione` <= '$dataMaggiore'
GROUP BY
    `nomeOperatore`,
    `modulo`
ORDER BY
    `nomeOperatore`,
    `modulo`";

//echo $queryCrmSede;
$risultatoCrmSede = $conn19->query($queryCrmSede);
while ($rigaCRM = $risultatoCrmSede->fetch_array()) {
    $operatore = $rigaCRM["operatore"];
    $idMandato = $rigaCRM["mandato"];
    $ok = $rigaCRM["OK"];
    $bkl = $rigaCRM["BKL"];
    $ko = $rigaCRM["KO"];

    $arrayVivigas[$operatore] = [
        'ok' => $ok,
        'bkl' => $bkl,
        'ko' => $ko,

    ];
}


$arrayUnion = [];
$queryCrmSede = "SELECT
    `nomeOperatore` AS operatore,
    `modulo` AS mandato,
 SUM(
        CASE  WHEN `Stato` = 'ok firma'  AND `idPratica` like 'Un%'   THEN 1 ELSE 0
    END
) AS OK,
SUM(
    CASE WHEN `Stato` IN(
        'In Attesa Firma',
        'ok vocal qc',
        'DA RIPROCESSARE',
        'DA FIRMARE'
    )  AND `idPratica` like 'Un%'   THEN 1 ELSE 0
END
) AS BKL,
SUM(
    CASE WHEN `Stato` IN(
        'Ko Recall',
        'Ko Vocal',
        'ko definitivo',
        'Ko Controllo Dati'
  
    )  AND `idPratica` like 'Un%'  THEN 1 ELSE 0
END
) AS KO

FROM
    `controlloBo`
WHERE
    `nomeOperatore` IN(
        'Alessandra Viola',
        'Caterina Di Fazio',
        'Francesco Ascone',
        'Martina Branca',
        'giuseppe macri',
        'valeria guadagnuolo',
    'Giorgia Barrese',
    'Gabriella Isabella',
    'Serena Ielapi',
    'Francesco Ieraci'
    ) AND `idPratica` like 'Un%'    
    AND `dataOperazione` >= '$dataMinore' 
    AND `dataOperazione` <= '$dataMaggiore'
GROUP BY
    `nomeOperatore`,
    `modulo`
ORDER BY
    `nomeOperatore`,
    `modulo`";

//echo $queryCrmSede;
$risultatoCrmSede = $conn19->query($queryCrmSede);
while ($rigaCRM = $risultatoCrmSede->fetch_array()) {
    $operatore = $rigaCRM["operatore"];
    $idMandato = $rigaCRM["mandato"];
    $ok = $rigaCRM["OK"];
    $bkl = $rigaCRM["BKL"];
    $ko = $rigaCRM["KO"];

    $arrayUnion [$operatore] = [
        'ok' => $ok,
        'bkl' => $bkl,
        'ko' => $ko,

    ];
}



$op = [
    "Alessandra Viola",
    "Caterina Di Fazio",
    "Francesco Ascone",
    "Martina Branca",
    "giuseppe macri",
    "valeria guadagnuolo",
    "Giorgia Barrese",
    "Gabriella Isabella",
    "Serena Ielapi",
    "Francesco Ieraci"
];

$caduta = 0;
function generaRigaHTML($operatore, $mandato, $arrayDati) {
    if (array_key_exists($operatore, $arrayDati)) {
        $okOperatore = $arrayDati[$operatore]['ok'] ?? 0;
        $koOperatore = $arrayDati[$operatore]['ko'] ?? 0;
        $bklOperatore = $arrayDati[$operatore]['bkl'] ?? 0;
    

        $caduta = ($koOperatore != 0) ? number_format(($koOperatore / ($okOperatore + $koOperatore)) * 100, 2) : 0;

        return "<tr>
            <td>$operatore</td>
            <td>$mandato</td>
            <td style='border-left: 2px solid lightslategray'>$okOperatore</td>
            <td style='border-left: 2px solid lightslategray'>$bklOperatore</td>
            <td style='border-left: 2px solid lightslategray'>$koOperatore</td>
            <td style='border-left: 2px solid lightslategray'>$caduta %</td>
        </tr>";
    }
    return ""; // Nessun dato disponibile per questo mandato
}

// Array associativo per mappare i mandati ai rispettivi array di dati
$mandatiArray = [
    "Plenitude" => $arrayPlenitude,
    "Vivigas Energia" => $arrayVivigas,
    "Union" => $arrayUnion
   
];

// Modulo selezionato dall'utente (ad esempio tramite una richiesta GET o POST)
$moduloSelezionato = $_POST['moduloSelezionato'] ?? null; // Usa il valore inviato o null se non selezionato

// Iterazione sugli operatori
foreach ($op as $operatore) {
    foreach ($mandatiArray as $mandato => $arrayDati) {
        // Filtra solo il modulo selezionato
        if ($moduloSelezionato === null || $moduloSelezionato === $mandato) {
            $html .= generaRigaHTML($operatore, $mandato, $arrayDati);
        }
    }
    // Riga di separazione (opzionale)
    $html .= "<tr style='background-color: orange;'><td colspan='6'></td></tr>";
}

echo $html;
?>  