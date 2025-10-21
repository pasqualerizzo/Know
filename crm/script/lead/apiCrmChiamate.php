<?php

$username = "bruno.cosentino";
$endpointUrl = "https://crm2.novaholding.it/webservice.php";
$key = "lxZyaXDahczHTsOH";

/*
 * Recupero del TOKEN usando bruno.cosentino come base
 */

function token() {
    $username = "bruno.cosentino";
    $endpointUrl = "https://crm2.novaholding.it/webservice.php";
    $query_fields = [
        "operation" => "getchallenge",
        "username" => $username
    ];
    $urlG = $endpointUrl . "?" . http_build_query($query_fields);
    $curlG = curl_init($urlG);
    curl_setopt($curlG, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curlG, CURLOPT_RETURNTRANSFER, true);
    $risultato = curl_exec($curlG);
    $json = json_decode($risultato, true);
    $token = $json['result']['token'];
    curl_close($curlG);
    return $token;
}

/*
 * Effettuo il login su crm
 */

function login() {
    $token = token();
    $key = "lxZyaXDahczHTsOH";
    $endpointUrl = "https://crm2.novaholding.it/webservice.php";
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

function importChiamate($_leadId, $_utmSource, $_utmCampagna, $_utmMedium, $_dataImport, $_telefono, $_agenzia, $_lista,$_key="GENERICO") { 
    $sessione = login();
    $endpointUrl = "https://crm2.novaholding.it/webservice.php";
    $param = array(
        "assigned_user_id" => "19x5",
        "name" => "APIChiamate",
        "cf_4459" => $_leadId, //LeadId            
        "cf_4451" => $_dataImport,
        "cf_4457" => $_telefono,
        "cf_4447" => $_utmSource,
        "cf_4443" => $_utmCampagna,
        "cf_4445" => $_utmMedium,
        "cf_4465" => $_agenzia,
        "cf_4461" => "new",
        "cf_4463" => $_dataImport,
        "cf_4475" => $_lista,
        "cf_4455"=>$_key,
        "cf_4687"=>"No",
    );
    $elements = json_encode($param);
    $params = array(
        "operation" => "create",
        "sessionName" => $sessione,
        "elementType" => "GestioneChiamata",
        "element" => $elements
    );
    $curl2 = curl_init($endpointUrl);
    curl_setopt($curl2, CURLOPT_POST, true);
    curl_setopt($curl2, CURLOPT_POSTFIELDS, $params);
    curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
    $risposta = curl_exec($curl2);
    $valori = json_decode($risposta, true);
    $id = $valori["result"]["gestionechiamatano"];
    curl_close($curl2);
    return $id;
}


function importChiamateDuplicato($_leadId, $_utmSource, $_utmCampagna, $_utmMedium, $_dataImport, $_telefono, $_agenzia, $_lista,$_duplicato,$_key="GENERICO") {
    $sessione = login();
    $endpointUrl = "https://crm2.novaholding.it/webservice.php";
    $param = array(
        "assigned_user_id" => "19x5",
        "name" => "APIChiamate",
        "cf_4459" => $_leadId, //LeadId            
        "cf_4451" => $_dataImport,
        "cf_4457" => $_telefono,
        "cf_4447" => $_utmSource,
        "cf_4443" => $_utmCampagna,
        "cf_4445" => $_utmMedium,
        "cf_4465" => $_agenzia,
        "cf_4461" => "new",
        "cf_4463" => $_dataImport,
        "cf_4475" => $_lista,
        "cf_4455"=>$_key,
        "cf_4687"=>$_duplicato,
    );
    $elements = json_encode($param);
    $params = array(
        "operation" => "create",
        "sessionName" => $sessione,
        "elementType" => "GestioneChiamata",
        "element" => $elements
    );
    $curl2 = curl_init($endpointUrl);
    curl_setopt($curl2, CURLOPT_POST, true);
    curl_setopt($curl2, CURLOPT_POSTFIELDS, $params);
    curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
    $risposta = curl_exec($curl2);
    $valori = json_decode($risposta, true);
    $id = $valori["result"]["gestionechiamatano"];
    curl_close($curl2);
    return $id;
}

?>