<?php

header('Access-Control-Allow-Origin: *');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

date_default_timezone_set('Europe/Rome');

error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$dataImport = date("Y-m-d H:i:s");

$obj = new Connessione();
$conn = $obj->apriConnessione();

$curl = curl_init();

$dataControllo = date('Y-m-d', strtotime(date('Y-m-d') . '-1 days'));
//$dataControllo ="2024-05-20";
$dataControllofine = date('Y-m-d');

$url = "https://business-api.tiktok.com/open_api/v1.2/reports/integrated/get/?";
$dimensions = json_encode(["campaign_id", "stat_time_day"]);
$metrics = json_encode(["spend", "impressions", "cpc", "cpm", "ctr", "reach", "conversion"]);
$idAccount = 7341425775561834498;
$get = [
    "advertiser_id" => $idAccount,
    "report_type" => "BASIC",
    "data_level" => "AUCTION_CAMPAIGN",
    "start_date" => $dataControllo,
    "end_date" => $dataControllo,
    "dimensions" => $dimensions,
    "metrics" => $metrics,
];

//echo $url . http_build_query($get);
curl_setopt_array($curl, array(
    CURLOPT_URL => $url . http_build_query($get),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Access-Token: d918d7bc63f015689a3da847bd823573eb1485f3'
    ),
));

$response = curl_exec($curl);

curl_close($curl);
//echo $response;
//
//echo "<br>";

$dati = json_decode($response, true);
//echo $dati;
if ($dati["message"] == "OK") {
    $valori = $dati["data"];
//    $lista=$valori["list"];
//    echo var_dump($lista);
    $liste = $valori["list"];

    foreach ($liste as $lista) {
        $dimensioni = $lista["dimensions"];
        $idCampagna = $dimensioni["campaign_id"];
        $metrica = $lista["metrics"];
        $importo_speso = $metrica["spend"];
        $risultati = $metrica["conversion"];
        $impression = $metrica["impressions"];
        $costo_per_risultato = $metrica["ctr"];
        $nomeCamapagna="Predefinito";
        switch ($idCampagna){
            case "1808001798584369":
                $nomeCamapagna="Seach_call_Chiediazero_TikTok";
        }
        //echo $nomeCamapagna;
        
        
        $queryStato = "INSERT INTO"
                . " `facebook`"
                . "(`giorno`, `id_account`, `nome_account`, `nome_campagna`, `importo_speso`, `risultati`, `impression`, `costo_per_risultato`,provenienza,gruppo)"
                . " VALUES"
                . " ('$dataControllo','$idAccount','DgtMedia','$nomeCamapagna','$importo_speso','$risultati','$impression','$costo_per_risultato','TikTok','$nomeCamapagna')";
        try {
            $risultato = $conn19->query($queryStato);
        } catch (exception $e) {
            echo "ex: " . $e;
            die();
        }
    }
}
$obj->chiudiConnessione();
$obj19->chiudiConnessione();
//echo "ok import effettuato";
