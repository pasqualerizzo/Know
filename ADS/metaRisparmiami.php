<?php

include '/Applications/MAMP/htdocs/Know/connessione/connessione.php';
include 'log.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

date_default_timezone_set('Europe/Rome');

error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$accountPublicitario = "147216197542152"; //Inserire Il numero di riferimento dell'account publicitario
$riferimentoAccountPublicitario = "act_" . $accountPublicitario;

$filds = "campaign_name,spend,impressions,clicks,account_name,conversions,actions";
$dataRicerca = "today";
$token = "EAAE3VQyGF2sBPQIs0Q3EALaC7qMDGZCfsX0bleiDDwlza7SDX81a0wmSkQc2MfEZA2izHwZAKhZAjnZAY5wr1w6GTYGteBLOvHCUcgqT4smjxr5zfh0VPiwUOOoZC9ZC1N2H2IZARcZBvIeCahzQfvO21zyXDLZAfwFEZAqptKvwB8uZB6HZCeRM6R6WmuZAqFTRjV";
$level="campaign";

$url = "https://graph.facebook.com/v23.0/$riferimentoAccountPublicitario/insights?fields=$filds&date_preset=$dataRicerca&access_token=$token&level=$level";
//echo $url;
$response = file_get_contents($url);
//echo $response;
$data = json_decode($response, true);

// Estrai i valori nella prima entry

foreach ($data['data'] as $campagnaData) {

    $spend       = $campagnaData['spend'] ?? '0.00';
    $impressions = $campagnaData['impressions'] ?? '0';
    $clicks      = $campagnaData['clicks'] ?? '0';
    $date_start  = $campagnaData['date_start'] ?? '';
    $date_stop   = $campagnaData['date_stop'] ?? '';
    $account     = $campagnaData['account_name'] ?? '';
    $campagna    = $campagnaData['campaign_name'] ?? $account;
    $conversione = $campagnaData['conversions'] ?? 0;

    $risultati = ($conversione == 0) ? 0 : $spend / $conversione;
    $chiamate=0;
    $chiamate20s=0;
    $form=0;
    
    if (isset($campagnaData['actions'])) {
    foreach ($campagnaData['actions'] as $azione) {
        if ($azione['action_type'] === 'click_to_call_native_call_placed') {
            $chiamate = $azione['value'];
            
        }elseif($azione['action_type'] === 'click_to_call_native_20s_call_connect'){
            $chiamate20s = $azione['value'];
           
        }elseif($azione['action_type'] === 'lead'){
            $form = $azione['value'];
            
        }
    }
}



    if(strpos($campagna, "C2C") !== false){
    $accountPersonalizzato = 'NovaDirect';
    }else{
         $accountPersonalizzato = 'NovaDirectForm';
    }

    $sql = "INSERT INTO facebook "
            . " (giorno, id_account,nome_account, nome_campagna, importo_speso, risultati, impression, costo_per_risultato,provenienza,gruppo,clicks,chiamate,chiamate20s,form) "
            . " VALUES "
            . "('$date_start','$accountPublicitario','$accountPersonalizzato','$campagna','$spend','$risultati','$impressions','$spend','Meta','$campagna','$clicks','$chiamate','$chiamate20s','$form')";
echo $sql."<br>";
$conn19->query($sql);
}
$obj19->chiudiConnessione();
?>