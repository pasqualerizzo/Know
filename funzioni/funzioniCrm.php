<?php

function login() {
    $token = token();
    $key = "N5RlbFw5yyXaTZMv";
    $endpointUrl = "https://crm.novaholding.it/webservice.php";
    $pars = [
        'operation' => "login",
        'username' => "bruno.cosentino",
        'accessKey' => md5($token . $key),
    ];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $endpointUrl);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $pars);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = json_decode(curl_exec($curl), true);
    $sessione = $response['result']['sessionName'];
    $userId = $response['result']['userId'];
    curl_close($curl);
    return $sessione;
}

/*
 * Creazione del dato su crm
 */

function importModulo($_arrayElementi, $_idOperatore, $_modulo) {
    $sessione = login();
    $endpointUrl = "https://crm.novaholding.it/webservice.php";
    $param = array(
        "assigned_user_id" => "19x" . $_idOperatore,
        "name" => $_modulo,
    );
    $param = array_merge($param, $_arrayElementi);
    $elements = json_encode($param);
    $params = array(
        "operation" => "create",
        "sessionName" => $sessione,
        "elementType" => "$_modulo",
        "element" => $elements
    );
    $curl2 = curl_init($endpointUrl);
    curl_setopt($curl2, CURLOPT_POST, true);
    curl_setopt($curl2, CURLOPT_POSTFIELDS, $params);
    curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl2);
    echo $response;

    $data = json_decode($response, true);
    $risposta = "";
    // Verifica se il campo "success" è true
    if (isset($data["success"]) && $data["success"] === true) {
        // Verifica se il campo "result" esiste e contiene l'ID
        if (isset($data["result"]["id"])) {
            $risposta = $data["result"]["id"]; // Restituisce l'ID
        } else {
            $risposta = "ID non trovato nella risposta.";
        }
    } else {
        $risposta = "Risposta non di successo.";
    }

    curl_close($curl2);

    return $risposta;
}

function token() {
    $username = "bruno.cosentino";
    $endpointUrl = "https://crm.novaholding.it/webservice.php";
    $query_fields = [
        "operation" => "getchallenge",
        "username" => $username
    ];
    $urlG = $endpointUrl . "?" . http_build_query($query_fields);
    echo $urlG;
    $curlG = curl_init($urlG);
    curl_setopt($curlG, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curlG, CURLOPT_RETURNTRANSFER, true);
    $risultato = curl_exec($curlG);
    
    $json = json_decode($risultato, true);
    $token = $json['result']['token'];
    curl_close($curlG);
    return $token;
}

?>
