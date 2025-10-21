<?php

function cercaTelefonoList($_telefono, $_lista) {
    $query_fields = [
        'user' => "apiuserid",
        'pass' => "apipass",
        'source' => 'api',
        'function' => "lead_search",
        'phone_number' => $_telefono,
    ];
    $url = "https://siscalllead.novadirect.it/vicidial/non_agent_api.php";
    $curl2 = curl_init($url . "?" . http_build_query($query_fields));
    curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl2);
    curl_close($curl2);

    $parti = explode("|", $response);
    if ((int) $parti[1] ==0) {
        return false;
    }
    $liste = explode("-", end($parti));
    return in_array($_lista, $liste);
}

function inserisciLead($_lista, $_telefono, $_nome,$_cognome,$_noteStatoLuce,$_noteStatoGas,$_codiceFiscale,$_dataContratto) {
    
    $commento = $_noteStatoLuce . " " . $_noteStatoGas;
    $query_fields = [
        'user' => "apiuserid",
        'pass' => "apipass",
        'source' => 'api',
        'function' => "add_lead",
        'add_to_hopper' => 'Y',
        'list_id' => $_lista,
        'phone_number' => $_telefono,
        'first_name' => $_nome,
        'last_name' => $_cognome,
        'comments' => $commento,
        'address1' => $_codiceFiscale,
        'address2' => $_dataContratto, 
        'duplicate_check'=>'DUPLIST',
    ];
    $url = "https://siscalllead.novadirect.it/vicidial/non_agent_api.php";
    $curl2 = curl_init($url . "?" . http_build_query($query_fields));
    curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl2);
    curl_close($curl2);

    if (strpos($response, 'SUCCESS: add_lead LEAD HAS BEEN ADDED') !== false) {
        return true;
    } else {
        return false;
    }
}

function inserisciLeadPDP($_lista, $_telefono, $_nome,$_cognome,$_noteStatoLuce,$_noteStatoGas,$_codiceFiscale,$_dataContratto,$_pod,$_pdr) {
    $pdp=$_pod." ".$_pdr;
    $commento = $_noteStatoLuce . " " . $_noteStatoGas;
    $query_fields = [
        'user' => "apiuserid",
        'pass' => "apipass",
        'source' => 'api',
        'function' => "add_lead",
        'add_to_hopper' => 'Y',
        'list_id' => $_lista,
        'phone_number' => $_telefono,
        'first_name' => $_nome,
        'last_name' => $_cognome,
        'comments' => $commento,
        'address1' => $_codiceFiscale,
        'address2' => $_dataContratto,
        'address3' => $pdp,
        'duplicate_check'=>'DUPLIST',
    ];
    $url = "https://siscalllead.novadirect.it/vicidial/non_agent_api.php";
    $curl2 = curl_init($url . "?" . http_build_query($query_fields));
    curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl2);
    curl_close($curl2);

    if (strpos($response, 'SUCCESS: add_lead LEAD HAS BEEN ADDED') !== false) {
        return true;
    } else {
        return false;
    }
}

function inserisciLeadPDPPagamento($_lista, $_telefono, $_nome,$_cognome,$_noteStatoLuce,$_noteStatoGas,$_codiceFiscale,$_dataContratto,$_pod,$_pdr,$_iban) {
    $pdp=$_pod." ".$_pdr;
    $commento = $_noteStatoLuce . " " . $_noteStatoGas;
    $query_fields = [
        'user' => "apiuserid",
        'pass' => "apipass",
        'source' => 'api',
        'function' => "add_lead",
        'add_to_hopper' => 'Y',
        'list_id' => $_lista,
        'phone_number' => $_telefono,
        'first_name' => $_nome,
        'last_name' => $_cognome,
        'comments' => $commento,
        'address1' => $_codiceFiscale,
        'address2' => $_dataContratto,
        'address3' => $pdp,
        'city'=>$_iban,
        'duplicate_check'=>'DUPLIST',
    ];
    $url = "https://siscalllead.novadirect.it/vicidial/non_agent_api.php";
    $curl2 = curl_init($url . "?" . http_build_query($query_fields));
    curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl2);
    curl_close($curl2);

    if (strpos($response, 'SUCCESS: add_lead LEAD HAS BEEN ADDED') !== false) {
        return true;
    } else {
        return false;
    }
}


function inserisciLeadKoNonValidati($_lista, $_telefono, $_nome,$_cognome,$_noteStatoLuce,$_noteStatoGas,$_codiceFiscale,$_dataContratto,$_pod,$_pdr,$_iban,$_indirizzo,$_tipoacquisizione,$_noteBackOffice) {
    $pdp=$_pod." ".$_pdr;
    $commento ="NL: ". $_noteStatoLuce . " NG: " . $_noteStatoGas." TA: ".$_tipoacquisizione." NB: ".$_noteBackOffice;
    $intestazione= $_nome." ".$_cognome;
    $query_fields = [
        'user' => "apiuserid",
        'pass' => "apipass",
        'source' => 'api',
        'function' => "add_lead",
        'add_to_hopper' => 'Y',
        'list_id' => $_lista,
        'phone_number' => $_telefono,
        'first_name' => $intestazione,
        'last_name' => $_codiceFiscale,
        'comments' => $commento,
        'address1' => $_indirizzo,
        'address2' => $_dataContratto,
        'address3' => $pdp,
        'city'=>$_iban,
        'duplicate_check'=>'DUPLIST',
    ];
    $url = "https://siscalllead.novadirect.it/vicidial/non_agent_api.php";
    $curl2 = curl_init($url . "?" . http_build_query($query_fields));
    curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl2);
    curl_close($curl2);

    if (strpos($response, 'SUCCESS: add_lead LEAD HAS BEEN ADDED') !== false) {
        return true;
    } else {
        return false;
    }
}
?>
