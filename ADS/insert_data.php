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

//function API
function callAPI($method, $url) {
    $curl = curl_init();
// OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
// EXECUTE:
    $result = curl_exec($curl);
    if (!$result) {
        $result = "Connection Failure";
    }
    curl_close($curl);
    return $result;
}

$sql = "SELECT auth_meta FROM accessi";
$risultato = $conn19->query($sql);

if (($publishers = $risultato->fetch_array())) {
    $authMeta = $publishers["auth_meta"];
} else {
    log_action("Authmeta non trovato", $conn);
}

$sql2 = "SELECT account,nome FROM accountPub";
$risultato2 = $conn19->query($sql2);
while ($riga = $risultato2->fetch_array()) {
    //echo var_dump($riga);
    $account_id_ads = $riga["account"];
    $account_nome_ads = $riga["nome"];
//    $link='https://graph.facebook.com/v19.0/act_' . $account_id_ads . '/insights?fields=impressions,spend,campaign_name,cost_per_action_type,conversions&level=campaign&time_range[since]=2025-03-01&time_range[until]=2025-03-01&access_token=' . $authMeta;
//    $link = 'https://graph.facebook.com/v19.0/act_' . $account_id_ads . '/insights?fields=impressions,spend,campaign_name,cost_per_action_type,conversions&level=campaign&date_preset=2025-03-15&access_token=' . $authMeta;
    $link = 'https://graph.facebook.com/v19.0/act_' . $account_id_ads . '/insights?fields=impressions,spend,campaign_name,cost_per_action_type,conversions,actions&level=campaign&date_preset=yesterday&access_token=' . $authMeta;
    
    echo $link;

    $get_data = callAPI('GET', $link);
    //echo $link;
    if ($get_data === "Connection Failure") {
        //log_action("Collegamento all'API non riuscito" . " - " . $riga["nome"], $conn19);
    } else {
        $output = json_decode($get_data, true);
        if (isset($output["error"])) {
            echo $output["error"]["message"];
            //log_action($output["error"]["message"] . " - " . $riga["nome"], $conn19);
        } else {
            foreach ($output["data"] as $element) {
                //echo var_dump($element);
                //echo "<br>";
                //echo $element["cost_per_action_type"][0]["action_type"];
                //if ($element["cost_per_action_type"][0]["action_type"] == "onsite_conversion.lead_grouped") {
                $cost_risult = $element["cost_per_action_type"][0]["value"];

                $risultati = $element["spend"] / $cost_risult;
                //echo $risultati;

                $data = $element["date_start"];

                    $nomeCamapagna = $element["campaign_name"];

                $spesa = $element["spend"];

                $impressione = $element["impressions"];
                //echo $impressione;
                $actions = $element["actions"];

                $clicks = $actions[7]["value"];

//                       
//                    } else {
//                        $cost_risult = 0;
//                        $risultati = 0;
//                    }
                $sql = "INSERT INTO facebook "
                        . " (giorno, id_account,nome_account, nome_campagna, importo_speso, risultati, impression, costo_per_risultato,provenienza,gruppo,clicks) "
                        . " VALUES "
                        . "('$data','$account_id_ads','$account_nome_ads','$nomeCamapagna','$spesa','$risultati','$impressione','$spe','Meta','$nomeCamapagna','$clicks')";
                echo $sql;
                $conn19->query($sql);
            }
        }
    }
}
$obj19->chiudiConnessione();
?>