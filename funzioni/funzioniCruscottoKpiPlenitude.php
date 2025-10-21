<?php

/**
 * recuperoEnel
 */
//function recuperoEnel($_conn, $_dataMaggiore, $_dataMinore) {
//    $risposta = [];
//
//    $query = "SELECT "
//            . " SUM(IF(aggiuntaEnel.fasePDA='OK',1,0)) as 'OK', "
//            . " SUM(IF(aggiuntaEnel.fasePDA='KO',1,0)) as 'KO',  "
//            . " count(*) as 'Tot',"
//            . " sum(convertito) as 'Convertito', "
//            . " sum(if(fasePost='OK',if(fasePDA='OK',pezzoLordo,0),0)) as 'OKPV',"
//            . " creatoDa as 'operatore'"
//            . " FROM "
//            . " `enel` "
//            . " inner join aggiuntaEnel on aggiuntaEnel.id=enel.id "
//            . " left join gestioneLead on gestioneLead.idSponsorizzata=enel.idGestioneLead "
//            . " where "
//            . " idGestioneLead like 'G%' "
//            . " and gestioneLead.dataImport <='$_dataMaggiore' "
//            . " and gestioneLead.dataImport >='$_dataMinore' "
//            . " group by "
//            . " creatoDa";
//
//    $risultato = $_conn->query($query);
//    while (($riga = $risultato->fetch_array())) {
//        $ok = $riga[0];
//        $ko = $riga[1];
//        $tot = $riga[2];
//        $convertito = $riga[3];
//        $okPv = $riga[4];
//        $operatore = $riga[5];
//        $risposta[$operatore] = [$ok, $ko, $tot, $convertito, $okPv];
//    }
//    return $risposta;
//}

function recuperoOre($_conn, $_dataMaggiore, $_dataMinore) {
    $risposta = [];
    $queryOre = "SELECT "
            . " full_name as operatore, "
            . " user_level AS livello, "
            . " territory AS citta, "
            . " campaign_description AS mandato, "
            . " SUM(CASE WHEN v.campaign_id = 'SPN_INB' OR v.campaign_id = 'VDF_TLCO' THEN (pause_sec + wait_sec + talk_sec + dispo_sec) ELSE 0 END) / 3600 AS 'oreSPN_VDF', "
            . " SUM(CASE WHEN v.campaign_id != 'SPN_INB' AND v.campaign_id != 'VDF_TLCO' THEN (pause_sec + wait_sec + talk_sec + dispo_sec) ELSE 0 END) / 3600 AS 'oreAltro' "
            . " FROM vicidial_agent_log AS v "
            . " INNER JOIN vicidial_users AS operatore ON v.user = operatore.user "
            . " INNER JOIN vicidial_campaigns AS campagna ON v.campaign_id = campagna.campaign_id "
            . " WHERE event_time >= '$_dataMinore' AND event_time <= '$_dataMaggiore' "
            . " GROUP BY full_name  ";
    //echo $queryOre;
    try {
        $risultatoOre = $_conn->query($queryOre);
    } catch (Exception $ex) {
        echo "Errore Siscall: " . $ex;
    }
    while (($rigaOre = $risultatoOre->fetch_array())) {
        $operatore = $rigaOre['operatore'];
        $oreIN = round($rigaOre['oreSPN_VDF'], 2);
        $oreOut = round($rigaOre['oreAltro'], 2);
        $sede = $rigaOre['citta'];
        if (!isset($livello)) {
            $livello = $rigaOre['livello'];
        }
        $risposta[$operatore] = [$oreIN, $oreOut, $sede, $livello];
    }
    return $risposta;
}

function calcoloRiferimento($_conn, $_dataMaggiore, $_dataMinore) {
    $queryRiferimento = "SELECT"
            . " count(idSponsorizzata) as Lead, "
            . " `pleniOk` AS OK_CP"
            . " FROM "
            . " `gestioneLead` "
            . " WHERE "
            . " `gestitoDa` NOT IN ( 'VDAD', '6666') "
            . " AND `CategoriaUltima` NOT IN ('NONUTILICHIUSI') "
            . " AND dataImport <= '$_dataMaggiore' "
            . " AND dataImport >= '$_dataMinore' "
            . " and idSponsorizzata like 'G%' "
            . " and (duplicato='no' or duplicato='') ";

//echo $queryRiferimento;
    $risultatoRiferimento = $_conn->query($queryRiferimento);
    if (($rigaRiferimento = $risultatoRiferimento->fetch_array())) {
        $leadRiferimento = $rigaRiferimento[0];
        $okRiferimerimento = $rigaRiferimento[1];

        $riferimento = ($leadRiferimento == 0) ? 0 : round(($okRiferimerimento / $leadRiferimento) * 100, 2);
    }
    return $riferimento;
}

function recuperoLead($_conn, $_dataMaggiore, $_dataMinore) {
    $risposta = [];

    $queryleadokfinale = "SELECT"
            . " gestitoDa AS operatore, "
            . " sum(if(duplicato='no',1,0)) AS Lead, "
            . " count(idSponsorizzata) as LeadLordo, "
            . " SUM(IF(`categoriaCampagna` = 'Energetico' AND duplicato='no', 1, 0)) AS Energetico, "
            . " SUM(IF(`categoriaCampagna` = 'Telco' AND duplicato='no' , 1, 0)) AS Telco "
            . " FROM "
            . " `gestioneLead` "
            . " WHERE "
            . " `gestitoDa` NOT IN ( 'VDAD', '6666') "
            . " AND `CategoriaUltima` NOT IN ('NONUTILICHIUSI') "
            . " AND dataImport <= '$_dataMaggiore' "
            . " AND dataImport >= '$_dataMinore' "
            . " and idSponsorizzata like 'G%' "
            . " group by "
            . " gestitoDa ";
    $risultatoCrm = $_conn->query($queryleadokfinale);

    while ($rigaCRM = $risultatoCrm->fetch_array()) {
        $operatore = $rigaCRM[0];
        $totaleLead = round($rigaCRM[1], 2);
        $leadLordo = round($rigaCRM[2], 2);
        $energetico = round($rigaCRM[3]);
        $telco = round($rigaCRM[4]);
        $risposta[$operatore] = [$totaleLead, $leadLordo, $energetico, $telco];
    }
    return $risposta;
}

function recuperoLeadVuoto($_conn, $_dataMaggiore, $_dataMinore) {
    $risposta = [];

    $queryleadokfinale = "SELECT"
           
            . " sum(if(duplicato='no',1,0)) AS Lead, "
            . " count(idSponsorizzata) as LeadLordo, "
            . " SUM(IF(`categoriaCampagna` = 'Energetico' AND duplicato='no', 1, 0)) AS Energetico, "
            . " SUM(IF(`categoriaCampagna` = 'Telco' AND duplicato='no' , 1, 0)) AS Telco "
            . " FROM "
            . " `gestioneLead` "
            . " WHERE "
            . " `gestitoDa` NOT IN ( 'VDAD', '6666') "
            . " AND `CategoriaUltima` NOT IN ('NONUTILICHIUSI') "
            . " AND dataImport <= '$_dataMaggiore' "
            . " AND dataImport >= '$_dataMinore' "
            . " and idSponsorizzata like 'G%' "
            . " AND gestitoDa in ('', 'Outbound Auto Dial')";
//            echo $queryleadokfinale;
    $risultatoCrm = $_conn->query($queryleadokfinale);

    while ($rigaCRM = $risultatoCrm->fetch_array()) {
        
        $totaleLead = round($rigaCRM[0], 2);
        $leadLordo = round($rigaCRM[1], 2);
        $energetico = round($rigaCRM[2]);
        $telco = round($rigaCRM[3]);
        $risposta[] = [$totaleLead, $leadLordo, $energetico, $telco];
    }
    return $risposta;
}


function recuperoLeadOperatore($_conn, $_dataMaggiore, $_dataMinore) {
    $risposta = [];
    $queryleadokfinaleWeek = "SELECT"
            . " sum(if(duplicato='no',1,0)) AS Lead, "
            . " (SUM(`pleniOk`) + SUM(`vodaOk`) + SUM(`viviOk`) + SUM(`irenOk`)+ SUM(`uniOk`)) AS OK_CP,"
            . " gestitoDa as operatore "
            . " FROM "
            . " `gestioneLead` "
            . " WHERE "
            . " `gestitoDa` NOT IN ('', 'VDAD', '6666') "
            . " AND `CategoriaUltima` NOT IN ('NONUTILICHIUSI') "
            . " AND dataImport <= '$_dataMaggiore' "
            . " AND dataImport >= '$_dataMinore' "
            . " and idSponsorizzata like 'G%' "
            . " and (duplicato='no' or duplicato='')"
            . " group by gestitoDa";

    $risultatoCrmWeek = $_conn->query($queryleadokfinaleWeek);
    if (($rigaWeek = $risultatoCrmWeek->fetch_array())) {
        $risposta[$rigaWeek[2]] = $rigaWeek[0];
    }
    return $risposta;
}

function recuperoPlenitude($_conn, $_dataMaggiore, $_dataMinore) {
    $risposta = [];

    $queryPleni = "SELECT "
            . " SUM(IF(aggiuntaPlenitude.fasePDA='OK',1,0)) as 'OK', "
            . " SUM(IF(aggiuntaPlenitude.fasePDA='KO',1,0)) as 'KO',  "
            . " count(*) as 'Tot',"
            . " sum(convertito) as 'Convertito', "
            . " sum(if(fasePost='OK',if(fasePDA='OK',pezzoLordo,0),0)) as 'OKPV',"
            . " creatoDa as 'operatore'"
            . " FROM "
            . " `plenitude` "
            . " inner join aggiuntaPlenitude on aggiuntaPlenitude.id=plenitude.id "
            . " left join gestioneLead on gestioneLead.idSponsorizzata=plenitude.idGestioneLead "
            . " where "
            . " idGestioneLead like 'G%' "
            . " and gestioneLead.dataImport <='$_dataMaggiore' "
            . " and gestioneLead.dataImport >='$_dataMinore' "
            . " and comodity  <>'Polizza' "
            . " group by "
            . " creatoDa";

    $risultatoPleni = $_conn->query($queryPleni);
    while (($riga = $risultatoPleni->fetch_array())) {
        $ok = $riga[0];
        $ko = $riga[1];
        $tot = $riga[2];
        $convertito = $riga[3];
        $okPv = $riga[4];
        $operatore = $riga[5];
        $risposta[$operatore] = [$ok, $ko, $tot, $convertito, $okPv];
    }
    return $risposta;
}


function recuperoPlenitudeOut($_conn, $_dataMaggiore, $_dataMinore) {
    $risposta = [];

    $query = "SELECT "
            . " SUM(IF(aggiuntaPlenitude.fasePDA='OK',1,0)) as 'OK', "
            . " SUM(IF(aggiuntaPlenitude.fasePDA='KO',1,0)) as 'KO',  "
            . " count(*) as 'Tot',"
            . " creatoDa as 'operatore' "
            . " FROM "
            . " `plenitude` "
            . " inner join aggiuntaPlenitude on aggiuntaPlenitude.id=plenitude.id "
            . " where "
            . " idGestioneLead not like 'G%' "
            . " and data <='$_dataMaggiore' "
            . " and data >='$_dataMinore' "
            . " and comodity  <>'Polizza' "
            . " group by "
            . " creatoDa";

    $risultato = $_conn->query($query);
    while (($riga = $risultato->fetch_array())) {
        $ok = $riga[0];
        $ko = $riga[1];
        $tot = $riga[2];

        $operatore = $riga[3];
        $risposta[$operatore] = [$ok, $ko, $tot];
    }
    return $risposta;
}


function elencoOperatore($_conn) {
    $operatore = [];
    $queryOperatori = "SELECT "
            . " full_name as operatore, "
            . " user_level AS livello, "
            . " territory AS citta, "
            . " user_group AS userGroup "
            . " "
            . " FROM vicidial_users AS v "
            . " WHERE (user_group like 'INB%' or user_group like 'FORM%' OR user_group like 'OP_%' OR user_group like 'Op_%' ) and active='Y' and full_name not like 'A_OP_%' "
            . " GROUP BY full_name "
            . " order by territory";
    $risultatoOperatori = $_conn->query($queryOperatori);
    while ($riga = $risultatoOperatori->fetch_array(MYSQLI_ASSOC)) {
        $operatore[$riga['operatore']] = [$riga['livello'], $riga['citta'], $riga['userGroup']];
    }
    return $operatore;
}

function elencoOperatoreSede($_conn,$_sede) {
    $operatore = [];
    $queryOperatori = "SELECT "
            . " full_name as operatore, "
            . " user_level AS livello, "
            . " territory AS citta, "
            . " user_group AS userGroup "
            . " "
            . " FROM vicidial_users AS v "
            . " WHERE (user_group like 'INB%' or user_group like 'FORM%' OR user_group like 'OP_%' OR user_group like 'Op_%' ) and active='Y' and full_name not like 'A_OP_%' AND territory='$_sede'"
            . " GROUP BY full_name ";
            
    $risultatoOperatori = $_conn->query($queryOperatori);
    while ($riga = $risultatoOperatori->fetch_array(MYSQLI_ASSOC)) {
        $operatore[$riga['operatore']] = [$riga['livello'], $riga['citta'], $riga['userGroup']];
    }
    return $operatore;
}


function recuperoPlenitudeData($_conn, $_dataMaggiore, $_dataMinore) {
    $risposta = [];

    $query = "SELECT "
            . " SUM(IF(aggiuntaPlenitude.fasePDA='OK',1,0)) as 'OK', "
            . " SUM(IF(aggiuntaPlenitude.fasePDA='KO',1,0)) as 'KO',  "
            . " count(*) as 'Tot',"
            . " creatoDa as 'operatore' "
            . " FROM "
            . " `plenitude` "
            . " inner join aggiuntaPlenitude on aggiuntaPlenitude.id=plenitude.id "
            . " where "
            . " idGestioneLead  like 'G%' "
            . " and data <='$_dataMaggiore' "
            . " and data >='$_dataMinore' "
            . " and comodity  <>'Polizza' "
            . " group by "
            . " creatoDa";
//echo $query;
    $risultato = $_conn->query($query);
    while (($riga = $risultato->fetch_array())) {
        $ok = $riga[0];
        $ko = $riga[1];
        $tot = $riga[2];

        $operatore = $riga[3];
        $risposta[$operatore] = [$ok, $ko, $tot];
    }
    return $risposta;
}


function siscallA15GG($_conn) {
    $dataOggi = date('Y-m-d');
    $dataMinore = date('Y-m-d 00:00:00', strtotime("-15 days " . $dataOggi));
    $dataMaggiore = date('Y-m-d 23:59:59', strtotime($dataOggi));

    $queryOre = "SELECT "
            . " full_name as operatore, "
            . " user_level AS livello, "
            . " territory AS citta, "
            . " campaign_description AS mandato, "
            . " SUM(CASE WHEN v.campaign_id = 'SPN_INB' OR v.campaign_id = 'VDF_TLCO' THEN (pause_sec + wait_sec + talk_sec + dispo_sec) ELSE 0 END) / 3600 AS 'oreSPN_VDF', "
            . " SUM(CASE WHEN v.campaign_id != 'SPN_INB' AND v.campaign_id != 'VDF_TLCO' THEN (pause_sec + wait_sec + talk_sec + dispo_sec) ELSE 0 END) / 3600 AS 'oreAltro' "
            . " FROM vicidial_agent_log AS v "
            . " INNER JOIN vicidial_users AS operatore ON v.user = operatore.user "
            . " INNER JOIN vicidial_campaigns AS campagna ON v.campaign_id = campagna.campaign_id "
            . " WHERE event_time >= '$dataMinore' AND event_time <= '$dataMaggiore' "
            . " GROUP BY full_name  ";
    //echo $queryOre;
    try {
        $risultatoOre = $conn->query($queryOre);
    } catch (Exception $ex) {
        echo "Errore Siscall2: " . $ex;
    }
    while (($rigaOre = $risultatoOre->fetch_array())) {
        $operatore = $rigaOre['operatore'];
        $oreIN = round($rigaOre['oreSPN_VDF'], 2);
        $oreOut = round($rigaOre['oreAltro'], 2);
        $sede = $rigaOre['citta'];
        if (!isset($livello)) {
            $livello = $rigaOre['livello'];
        }
        $siscall2[$operatore] = [$oreIN, $oreOut, $sede, $livello];
    }
}

function giorniLavorati15gg($_conn) {
    $risposta = [];
    $oggi = date('Y-m-d');
    $ieri = date('Y-m-d', strtotime($oggi . " -1 days"));
    $giorni15 = date('Y-m-d', strtotime($oggi . " -15 days"));
    $queryOre = "SELECT 
            nomeCompleto,
            
            COUNT(DISTINCT CASE WHEN giorno BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 16 DAY) AND DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY) THEN DATE(giorno)  END) AS giorni_lavorati,
            SUM(CASE WHEN giorno BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 16 DAY) AND DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY) THEN numero  ELSE 0  END) / 3600 AS ore_giorni_lavorati 
        FROM 
            `stringheTotale` 
        WHERE 
        
            mandato = 'Lead Inbound'  
        GROUP BY nomeCompleto;";
}



function recuperoOre16GG($_conn) {
    $risposta = [];
    $ieri = date('Y-m-d', strtotime('-1 days'));
    $data16 = date('Y-m-d', strtotime('-16 days'));
    $queryOre = "SELECT 
            nomeCompleto,            
            COUNT(DISTINCT(giorno)) AS giorni_lavorati,
            SUM(numero) / 3600 AS ore_giorni_lavorati 
        FROM 
            `stringheTotale` 
        WHERE 
        giorno BETWEEN '$data16' AND '$ieri'
           AND  mandato = 'Lead Inbound'  
        GROUP BY nomeCompleto";
    $risultatoOre = $_conn->query($queryOre);
    while (($rigaOre = $risultatoOre->fetch_array())) {
        $operatore = $rigaOre[0];
        $gglavin = $rigaOre[1];
        $oregglavin = round($rigaOre[2], 2);
        $risposta[$operatore] = [$gglavin, $oregglavin];
    }
    return $risposta;
}

?>
