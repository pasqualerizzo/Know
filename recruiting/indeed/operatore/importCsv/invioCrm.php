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

/*
 * Creazione del dato su crm
 */

function importRecruiting($_leadId, $_nome, $_mail, $_sede, $_source, $_ruolo, $_data, $_esperienza, $_pc, $_lingua) {
    $sessione = login();
    $endpointUrl = "https://crm2.novaholding.it/webservice.php";
    $param = array(
        "assigned_user_id" => "19x5",
        "name" => "p",
        "cf_2701" => $_leadId, //LeadId
        "cf_2711" => $_nome, //nome
        "cf_2715" => $_mail, //mail
        "cf_2707" => $_sede, //sede
        "cf_2767" => $_source, //sorce
        //"cf_2709" => $_ruolo, modificato a seguito errore di Roberto Saladini il 20/09/2023
        "cf_3891" => $_ruolo,
        "cf_2705" => $_data,
        "cf_2727" => $_esperienza,
        "cf_2782" => 'no',
        "cf_3526" => $_lingua,
        "cf_2729" => $_pc
    );
    $elements = json_encode($param);
    $params = array(
        "operation" => "create",
        "sessionName" => $sessione,
        "elementType" => "RecruitingHR",
        "element" => $elements
    );
    $curl2 = curl_init($endpointUrl);
    curl_setopt($curl2, CURLOPT_POST, true);
    curl_setopt($curl2, CURLOPT_POSTFIELDS, $params);
    curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
    curl_exec($curl2);
    curl_close($curl2);
}

function importRecruitingUTM($_leadId, $_nome, $_mail, $_sede, $_source, $_ruolo, $_data, $_esperienza, $_pc, $_lingua, $_utmSource, $_utmCampagna, $_utmMedium) {
    $sessione = login();
    $endpointUrl = "https://crm2.novaholding.it/webservice.php";
    $param = array(
        "assigned_user_id" => "19x5",
        "name" => "p",
        "cf_2701" => $_leadId, //LeadId
        "cf_2711" => $_nome, //nome
        "cf_2715" => $_mail, //mail
        "cf_2707" => $_sede, //sede
        "cf_2767" => $_source, //sorce
        //"cf_2709" => $_ruolo, modificato a seguito errore di Roberto Saladini il 20/09/2023
        "cf_3891" => $_ruolo,
        "cf_2705" => $_data,
//        "cf_2727" => $_esperienza,
        "cf_2782" => 'no',
//        "cf_3526" => $_lingua,
//        "cf_2729" => $_pc,
        "cf_3895" => $_utmSource,
        "cf_3897" => $_utmCampagna,
        "cf_3899" => $_utmMedium,
    );
    $elements = json_encode($param);
    $params = array(
        "operation" => "create",
        "sessionName" => $sessione,
        "elementType" => "RecruitingHR",
        "element" => $elements
    );
    $curl2 = curl_init($endpointUrl);
    curl_setopt($curl2, CURLOPT_POST, true);
    curl_setopt($curl2, CURLOPT_POSTFIELDS, $params);
    curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
    curl_exec($curl2);
    curl_close($curl2);
}

function importRecruitingUTMLog($_leadId, $_nome, $_mail, $_sede, $_source, $_ruolo, $_data, $_esperienza, $_pc, $_lingua, $_utmSource, $_utmCampagna, $_utmMedium, $_log) {
    $sessione = login();
    $endpointUrl = "https://crm2.novaholding.it/webservice.php";
    $param = array(
        "assigned_user_id" => "19x5",
        "name" => "p",
        "cf_2701" => $_leadId, //LeadId
        "cf_2711" => $_nome, //nome
        "cf_2715" => $_mail, //mail
        "cf_2707" => $_sede, //sede
        "cf_2767" => $_source, //sorce
        //"cf_2709" => $_ruolo, modificato a seguito errore di Roberto Saladini il 20/09/2023
        "cf_3891" => $_ruolo,
        "cf_2705" => $_data,
        "cf_2727" => $_esperienza,
        "cf_2782" => 'no',
        "cf_3526" => $_lingua,
        "cf_2729" => $_pc,
        "cf_3895" => $_utmSource,
        "cf_3897" => $_utmCampagna,
        "cf_3899" => $_utmMedium,
        "cf_3901" => $_log
    );
    $elements = json_encode($param);
    $params = array(
        "operation" => "create",
        "sessionName" => $sessione,
        "elementType" => "RecruitingHR",
        "element" => $elements
    );
    $curl2 = curl_init($endpointUrl);
    curl_setopt($curl2, CURLOPT_POST, true);
    curl_setopt($curl2, CURLOPT_POSTFIELDS, $params);
    curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl2);
    echo $response;
    curl_close($curl2);
}

function importRecruitingUTMLogCv($_leadId, $_nome, $_mail, $_sede, $_source, $_ruolo, $_data, $_esperienza, $_pc, $_lingua, $_utmSource, $_utmCampagna, $_utmMedium, $_log, $_cv) {
    $sessione = login();
    $endpointUrl = "https://crm2.novaholding.it/webservice.php";
    $param = array(
        "assigned_user_id" => "19x5",
        "name" => "p",
        "cf_2701" => $_leadId, //LeadId
        "cf_2711" => $_nome, //nome
        "cf_2715" => $_mail, //mail
        "cf_2707" => $_sede, //sede
        "cf_2767" => $_source, //sorce
        //"cf_2709" => $_ruolo, modificato a seguito errore di Roberto Saladini il 20/09/2023
        "cf_3891" => $_ruolo,
        "cf_2705" => $_data,
        "cf_2727" => $_esperienza,
        "cf_2782" => 'no',
        "cf_3526" => $_lingua,
        "cf_2729" => $_pc,
        "cf_3895" => $_utmSource,
        "cf_3897" => $_utmCampagna,
        "cf_3899" => $_utmMedium,
        "cf_3901" => $_log,
        "cf_4481" => $_cv
    );
    $elements = json_encode($param);
    $params = array(
        "operation" => "create",
        "sessionName" => $sessione,
        "elementType" => "RecruitingHR",
        "element" => $elements
    );
    $curl2 = curl_init($endpointUrl);
    curl_setopt($curl2, CURLOPT_POST, true);
    curl_setopt($curl2, CURLOPT_POSTFIELDS, $params);
    curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl2);
    echo $response;
    curl_close($curl2);
}

function importSponsorizzate($_leadId, $_nome, $_cognome, $_mail, $_source, $_utmSource, $_utmCampagna, $_utmMedium, $_log, $_origine, $_brand, $_dataImport, $_ip) {
    $sessione = login();
    $endpointUrl = "https://crm2.novaholding.it/webservice.php";
    $param = array(
        "assigned_user_id" => "19x5",
        "name" => "sponsorizzate",
        "cf_4066" => $_leadId, //LeadId
        "cf_4044" => $_nome, //nome
        "cf_4046" => $_cognome, //nome
        "cf_4048" => $_mail, //mail        
        "cf_4068" => $_source, //sorce        
        "cf_4058" => $_dataImport,
        "cf_4064" => $_log,
        "cf_4054" => $_utmSource,
        "cf_4050" => $_utmCampagna,
        "cf_4048" => $_utmMedium,
        "cf_4056" => $_ip,
        "cf_4060" => $_origine,
        "cf_4062" => $_brand,
    );
    $elements = json_encode($param);
    $params = array(
        "operation" => "create",
        "sessionName" => $sessione,
        "elementType" => "GestioneLead",
        "element" => $elements
    );
    $curl2 = curl_init($endpointUrl);
    curl_setopt($curl2, CURLOPT_POST, true);
    curl_setopt($curl2, CURLOPT_POSTFIELDS, $params);
    curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
    $risposta = curl_exec($curl2);
    curl_close($curl2);

    echo $risposta;
}


function importChiamateRecruiting($_leadId, $_utmSource, $_utmCampagna, $_utmMedium, $_dataImport, $_telefono) {
    $sessione = login();
    $endpointUrl = "https://crm2.novaholding.it/webservice.php";
    $param = array(
        "assigned_user_id" => "19x5",
        "name" => "Inbound",
        "cf_2701" => $_leadId, //LeadId 
        "cf_3901" => $_telefono,
        "cf_2713" => $_telefono,
        "cf_2711" => "INBOUND",
        "cf_3895" => $_utmSource,
        "cf_3897" => $_utmCampagna,
        "cf_3899" => $_utmMedium,
        "cf_2705" => $_dataImport,
        "cf_2782"=>"no"
    );
    $elements = json_encode($param);
    $params = array(
        "operation" => "create",
        "sessionName" => $sessione,
        "elementType" => "RecruitingHR",
        "element" => $elements
    );
    $curl2 = curl_init($endpointUrl);
    curl_setopt($curl2, CURLOPT_POST, true);
    curl_setopt($curl2, CURLOPT_POSTFIELDS, $params);
    curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
    $risposta = curl_exec($curl2);
    echo $risposta;
    //$valori = json_decode($risposta, true);
    //$id = $valori["result"]["Recruitinghrno"];
    curl_close($curl2);
    //return $id;
}
?>