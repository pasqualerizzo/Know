
<?php

function mediaRiferimento($_conn, $_dataMaggiore, $_dataMinore) {

    $leadTotali = leadTotali($_conn, $_dataMaggiore, $_dataMinore);
    $okPleni = okPleni($_conn, $_dataMaggiore, $_dataMinore);
    $okEnel = okEnel($_conn, $_dataMaggiore, $_dataMinore);
    $okVivi = okVivigas($_conn, $_dataMaggiore, $_dataMinore);
    $okIren = okIren($_conn, $_dataMaggiore, $_dataMinore);
    $okUnion = okUnion($_conn, $_dataMaggiore, $_dataMinore);
    $okVoda = okVodafone($_conn, $_dataMaggiore, $_dataMinore);

    $okTotali = $okPleni + $okVivi + $okIren + $okUnion + $okVoda + $okEnel;
    $mediaRiferimento = ($leadTotali == 0) ? 0 : round(($okTotali / $leadTotali) * 100, 2);
    $ritorno = [
        'mediaRiferimento' => $mediaRiferimento,
        'leadTotali' => $leadTotali,
        'okTotali' => $okTotali,
    ];
    return $ritorno;
}
/**
 * 
 * @param type $_conn
 * @param type $_dataMaggiore
 * @param type $_dataMinore
 * @return Totale lead
 * 
 * 
 * 
 */
function leadTotali($_conn, $_dataMaggiore, $_dataMinore) {
    $queryTotale = "SELECT"
            . " COUNT(`idSponsorizzata`) AS Lead "
              
            . " FROM "
            . " `gestioneLead` "
            . " WHERE "
          
            . "  `CategoriaUltima` NOT IN ('NONUTILICHIUSI') "
            . " AND dataImport <= '$_dataMaggiore' "
            . " AND dataImport >= '$_dataMinore' "
            . " and idSponsorizzata like 'GCL%' "
            . " and (duplicato='no' or duplicato='') "
             . "and `gestitoDa` NOT IN ( 'VDAD', '6666') ";

    $risultatoTotale = $_conn->query($queryTotale);
    $rigaTotale = $risultatoTotale->fetch_array();
    return $rigaTotale[0];
}

function okPleni($_conn, $_dataMaggiore, $_dataMinore) {
    $queryPleni = "SELECT "
            . " SUM(IF(aggiuntaPlenitude.fasePDA='OK',1,0)) as 'OK' "
            . " FROM "
            . " `plenitude` "
            . " inner join aggiuntaPlenitude on aggiuntaPlenitude.id=plenitude.id "
            . " left join gestioneLead on gestioneLead.idSponsorizzata=plenitude.idGestioneLead "
            . " where "
            . " idGestioneLead like 'GCL%' "
            . " and gestioneLead.dataImport <='$_dataMaggiore' "
            . " and gestioneLead.dataImport >='$_dataMinore' ";

//echo $queryPleni;

    $risultatoPleni = $_conn->query($queryPleni);
    if (($rigaPleni = $risultatoPleni->fetch_array())) {
        $okPleni = $rigaPleni[0];
    } else {
        $okPleni = 0;
    }
    return $okPleni;
}

function okEnel($_conn, $_dataMaggiore, $_dataMinore) {
    $queryEnel = "SELECT "
            . " SUM(IF(aggiuntaEnel.fasePDA='OK',1,0)) as 'OK' "
            . " FROM "
            . " `enel` "
            . " inner join aggiuntaEnel on aggiuntaEnel.id=enel.id "
            . " left join gestioneLead on gestioneLead.idSponsorizzata=enel.idGestioneLead "
            . " where "
            . " idGestioneLead like 'GCL%' "
            . " and gestioneLead.dataImport <='$_dataMaggiore' "
            . " and gestioneLead.dataImport >='$_dataMinore' ";

//echo $queryPleni;

    $risultatoEnel = $_conn->query($queryEnel);
    if (($rigaEnel = $risultatoEnel->fetch_array())) {
        $okEnel = $rigaEnel[0];
    } else {
        $okEnel = 0;
    }
    return $okEnel;
}

function okVivigas($_conn, $_dataMaggiore, $_dataMinore) {
    $queryvivigas = "SELECT "
            . " SUM(IF(aggiuntaVivigas.fasePDA='OK',1,0)) as 'OK' "
            . " FROM "
            . " `vivigas` "
            . " inner join aggiuntaVivigas on aggiuntaVivigas.id=vivigas.id "
            . " left join gestioneLead on gestioneLead.idSponsorizzata=vivigas.idGestioneLead "
            . " where "
            . " idGestioneLead like 'GCL%' "
            . " and gestioneLead.dataImport <='$_dataMaggiore' "
            . " and gestioneLead.dataImport >='$_dataMinore' ";

    $risultatoVivi = $_conn->query($queryvivigas);
    if (($rigaVivi = $risultatoVivi->fetch_array())) {
        $okVivi = $rigaVivi[0];
    } else {
        $okVivi = 0;
    }
    return $okVivi;
}

function okIren($_conn, $_dataMaggiore, $_dataMinore) {
    $queryIren = "SELECT "
            . " SUM(IF(aggiuntaIren.fasePDA='OK',1,0)) as 'OK' "
            . " FROM "
            . " `iren` "
            . " inner join aggiuntaIren on aggiuntaIren.id=iren.id "
            . " left join gestioneLead on gestioneLead.idSponsorizzata=iren.idGestioneLead "
            . " where "
            . " idGestioneLead like 'GCL%' "
            . " and gestioneLead.dataImport <='$_dataMaggiore' "
            . " and gestioneLead.dataImport >='$_dataMinore' ";

    $risultatoIren = $_conn->query($queryIren);
    if (($rigaIren = $risultatoIren->fetch_array())) {
        $okIren = $rigaIren[0];
    } else {
        $okIren = 0;
    }
    return $okIren;
}

function okUnion($_conn, $_dataMaggiore, $_dataMinore) {
    $queryUnion = "SELECT "
            . " SUM(IF(aggiuntaUnion.fasePDA='OK',1,0)) as 'OK' "
            . " FROM "
            . " `union` "
            . " inner join aggiuntaUnion on aggiuntaUnion.id=union.id "
            . " left join gestioneLead on gestioneLead.idSponsorizzata=union.idGestioneLead "
            . " where "
            . " idGestioneLead like 'GCL%' "
            . " and gestioneLead.dataImport <='$_dataMaggiore' "
            . " and gestioneLead.dataImport >='$_dataMinore' ";

    $risultatoUnion = $_conn->query($queryUnion);
    if (($rigaUnion = $risultatoUnion->fetch_array())) {
        $okUnion = $rigaUnion[0];
    } else {
        $okUnion = 0;
    }
    return $okUnion;
}

function okVodafone($_conn, $_dataMaggiore, $_dataMinore) {
    $queryVoda = "SELECT "
            . " SUM(IF(aggiuntaVodafone.fasePDA='OK',1,0)) as 'OK' "
            . " FROM "
            . " `vodafone` "
            . " inner join aggiuntaVodafone on aggiuntaVodafone.id=vodafone.id "
            . " left join gestioneLead on gestioneLead.idSponsorizzata=vodafone.idGestioneLead "
            . " where "
            . " idGestioneLead like 'GCL%' "
            . " and gestioneLead.dataImport <='$_dataMaggiore' "
            . " and gestioneLead.dataImport >='$_dataMinore' ";

    $risultatoVoda = $_conn->query($queryVoda);
    if (($rigaVoda = $risultatoVoda->fetch_array())) {
        $okVoda = $rigaVoda[0];
    } else {
        $okVoda = 0;
    }
    return $okVoda;
}

function okPlenitudeOperatore($_conn, $_dataMaggiore, $_dataMinore, $_operatore) {

    $queryPleni = "SELECT "
            . " count(aggiuntaPlenitude.fasePDA) AS 'OKIN' "
            . " FROM "
            . " `plenitude` "
            . " inner join aggiuntaPlenitude on aggiuntaPlenitude.id=plenitude.id "
            . " left join gestioneLead on gestioneLead.idSponsorizzata=plenitude.idGestioneLead "
            . " where "
            . "  gestioneLead.dataImport <='$_dataMaggiore' "
            . " and gestioneLead.dataImport >='$_dataMinore' "
            . " and creatoDa='$_operatore' "
            . " and aggiuntaPlenitude.fasePDA = 'OK' "
            . " and plenitude.idGestioneLead LIKE 'GCL%' ";

//echo $queryPleni;

    $risultatoPleni = $_conn->query($queryPleni);
    if (($rigaPleni = $risultatoPleni->fetch_array())) {
        $okPleni = $rigaPleni[0];
    } else {
        $okPleni = 0;
    }
    return $okPleni;
}

function okEnelOperatore($_conn, $_dataMaggiore, $_dataMinore, $_operatore) {
    $queryEnel = "SELECT "
            . " count(aggiuntaEnel.fasePDA) AS 'OKIN' "
            . " FROM "
            . " `enel` "
            . " inner join aggiuntaEnel on aggiuntaEnel.id=enel.id "
            . " left join gestioneLead on gestioneLead.idSponsorizzata=enel.idGestioneLead "
            . " where "
            . "  gestioneLead.dataImport <='$_dataMaggiore' "
            . " and gestioneLead.dataImport >='$_dataMinore' "
            . " and creatoDa='$_operatore' "
            . " and aggiuntaEnel.fasePDA = 'OK' "
            . " and enel.idGestioneLead LIKE 'GCL%'";

//echo $queryEnel;

    $risultatoEnel = $_conn->query($queryEnel);
    if (($rigaEnel = $risultatoEnel->fetch_array())) {
        $okEnel = $rigaEnel[0];
//echo $okEnel;
    } else {
        $okEnel = 0;
    }
    return $okEnel;
}

function okOutPlenitudeOperatore($_conn, $_dataMaggioreOre, $_dataMinoreOre, $_operatore) {
    $queryPleni = "SELECT "
            . " SUM(IF(aggiuntaPlenitude.fasePDA = 'OK' AND plenitude.idGestioneLead NOT LIKE 'GCL%',1,0)) AS 'OKOUT' "
            . " FROM "
            . " `plenitude` "
            . " inner join aggiuntaPlenitude on aggiuntaPlenitude.id=plenitude.id "
            . " where "
            . "  data <='$_dataMaggioreOre' "
            . " and data >='$_dataMinoreOre' "
            . " and creatoDa='$_operatore' "
            . " group by "
            . " creatoDa";

//echo $queryPleni;

    $risultatoPleni = $_conn->query($queryPleni);
    if (($rigaPleni = $risultatoPleni->fetch_array())) {

        $okPleniOut = $rigaPleni[0];
    } else {

        $okPleniOut = 0;
    }
    return $okPleniOut;
}

function okOutEnelOperatore($_conn, $_dataMaggioreOre, $_dataMinoreOre, $_operatore) {
    $queryEnelO = "SELECT "
            . " SUM(IF(aggiuntaEnel.fasePDA = 'OK' AND enel.idGestioneLead NOT LIKE 'GCL%',1,0)) AS 'OKOUT' "
            . " FROM "
            . " `enel` "
            . " inner join aggiuntaEnel on aggiuntaEnel.id=enel.id "
            . " where "
            . "  data <='$_dataMaggioreOre' "
            . " and data >='$_dataMinoreOre' "
            . " and creatoDa='$_operatore' "
            . " group by "
            . " creatoDa";

//echo $queryPleni;

    $risultatoEnelO = $_conn->query($queryEnelO);
    if (($rigaEnelO = $risultatoEnelO->fetch_array())) {

        $okEnelOut = $rigaEnelO[0];
    } else {

        $okEnelOut = 0;
    }
    return $okEnelOut;
}

function okVivigasOperatore($_conn, $_dataMaggiore, $_dataMinore, $_operatore) {
    $queryvivigas = "SELECT "
            . " SUM(IF(aggiuntaVivigas.fasePDA='OK' AND vivigas.idGestioneLead LIKE 'GCL%',1,0)) as 'OKIN' "
            . " FROM "
            . " `vivigas` "
            . " inner join aggiuntaVivigas on aggiuntaVivigas.id=vivigas.id "
            . " left join gestioneLead on gestioneLead.idSponsorizzata=vivigas.idGestioneLead "
            . " where "
            . "  gestioneLead.dataImport <='$_dataMaggiore' "
            . " and gestioneLead.dataImport >='$_dataMinore' "
            . " and creatoDa='$_operatore' "
            . " group by "
            . " creatoDa";

    $risultatoVivi = $_conn->query($queryvivigas);
    if (($rigaVivi = $risultatoVivi->fetch_array())) {
        $okVivi = $rigaVivi[0];
    } else {
        $okVivi = 0;
    }
    return $okVivi;
}

function okOutVivigasOperatore($_conn, $_dataMaggioreOre, $_dataMinoreOre, $_operatore) {
    $queryvivigas = "SELECT "
            . " SUM(IF(aggiuntaVivigas.fasePDA='OK' AND vivigas.idGestioneLead NOT LIKE 'GCL%',1,0)) as 'OKOUT' "
            . " FROM "
            . " `vivigas` "
            . " inner join aggiuntaVivigas on aggiuntaVivigas.id=vivigas.id "
            . " where "
            . "  data <='$_dataMaggioreOre' "
            . " and data >='$_dataMinoreOre' "
            . " and creatoDa='$_operatore' "
            . " group by "
            . " creatoDa";

    $risultatoVivi = $_conn->query($queryvivigas);
    if (($rigaVivi = $risultatoVivi->fetch_array())) {

        $okViviOut = $rigaVivi[0];
    } else {

        $okViviOut = 0;
    }
    return $okViviOut;
}

function okIrenOperatore($_conn, $_dataMaggiore, $_dataMinore, $_operatore) {
    $queryIren = "SELECT "
            . " SUM(IF(aggiuntaIren.fasePDA='OK' AND iren.idGestioneLead LIKE 'GCL%',1,0)) as 'OKIN' "
            . " FROM "
            . " `iren` "
            . " inner join aggiuntaIren on aggiuntaIren.id=iren.id "
            . " left join gestioneLead on gestioneLead.idSponsorizzata=iren.idGestioneLead "
            . " where "
            . "  gestioneLead.dataImport <='$_dataMaggiore' "
            . " and gestioneLead.dataImport >='$_dataMinore' "
            . " and creatoDa='$_operatore' "
            . " group by "
            . " creatoDa";

    $risultatoIren = $_conn->query($queryIren);
    if (($rigaIren = $risultatoIren->fetch_array())) {
        $okIren = $rigaIren[0];
    } else {
        $okIren = 0;
    }
    return $okIren;
}

function okOuitIrenOperatore($_conn, $_dataMaggiore, $_dataMinore, $_operatore) {
    $queryIren = "SELECT "
            . " SUM(IF(aggiuntaIren.fasePDA='OK' AND iren.idGestioneLead NOT LIKE 'GCL%',1,0)) as 'OKOUT' "
            . " FROM "
            . " `iren` "
            . " inner join aggiuntaIren on aggiuntaIren.id=iren.id "
            . " where "
            . "  data <='$_dataMaggiore' "
            . " and data >='$_dataMinore' "
            . " and creatoDa='$_operatore' "
            . " group by "
            . " creatoDa";

    $risultatoIren = $_conn->query($queryIren);
    if (($rigaIren = $risultatoIren->fetch_array())) {

        $okIrenOut = $rigaIren[0];
    } else {

        $okIrenOut = 0;
    }
    return $okIrenOut;
}

function okUnionOperatore($_conn, $_dataMaggiore, $_dataMinore, $_operatore) {
    $queryUnion = "SELECT "
            . " SUM(IF(aggiuntaUnion.fasePDA='OK' AND union.idGestioneLead LIKE 'GCL%',1,0)) as 'OKIN' "
            . " FROM "
            . " `union` "
            . " inner join aggiuntaUnion on aggiuntaUnion.id=union.id "
            . " left join gestioneLead on gestioneLead.idSponsorizzata=union.idGestioneLead "
            . " where "
            . "  gestioneLead.dataImport <='$_dataMaggiore' "
            . " and gestioneLead.dataImport >='$_dataMinore' "
            . " and creatoDa='$_operatore' "
            . " group by "
            . " creatoDa";

    $risultatoUnion = $_conn->query($queryUnion);
    if (($rigaUnion = $risultatoUnion->fetch_array())) {
        $okUnion = $rigaUnion[0];
    } else {
        $okUnion = 0;
    }
    return $okUnion;
}

function okOutUnionOperatore($_conn, $_dataMaggiore, $_dataMinore, $_operatore) {
    $queryUnion = "SELECT "
            . " SUM(IF(aggiuntaUnion.fasePDA='OK' AND union.idGestioneLead NOT LIKE 'GCL%',1,0)) as 'OKOUT' "
            . " FROM "
            . " `union` "
            . " inner join aggiuntaUnion on aggiuntaUnion.id=union.id "
            . " where "
            . " data <='$_dataMaggiore' "
            . " and data >='$_dataMinore' "
            . " and creatoDa='$_operatore' "
            . " group by "
            . " creatoDa";

    $risultatoUnion = $_conn->query($queryUnion);
    if (($rigaUnion = $risultatoUnion->fetch_array())) {

        $okUnionOut = $rigaUnion[0];
    } else {

        $okUnionOut = 0;
    }
    return$okUnionOut;
}

function okVodafoneOperatore($_conn, $_dataMaggiore, $_dataMinore, $_operatore) {
    $queryVoda = "SELECT "
            . " SUM(IF(aggiuntaVodafone.fasePDA='OK' AND vodafone.idGestioneLead LIKE 'GCL%',1,0)) as 'OKIN' "
            . " FROM "
            . " `vodafone` "
            . " inner join aggiuntaVodafone on aggiuntaVodafone.id=vodafone.id "
            . " left join gestioneLead on gestioneLead.idSponsorizzata=vodafone.idGestioneLead "
            . " where "
            . "  gestioneLead.dataImport <='$_dataMaggiore' "
            . " and gestioneLead.dataImport >='$_dataMinore' "
            . " and creatoDa='$_operatore' "
            . " group by "
            . " creatoDa";

    $risultatoVoda = $_conn->query($queryVoda);
    if (($rigaVoda = $risultatoVoda->fetch_array())) {
        $okVoda = $rigaVoda[0];
    } else {
        $okVoda = 0;
    }
    return $okVoda;
}

function okOutVodafoneOperatore($_conn, $_dataMaggioreOre, $_dataMinoreOre, $_operatore) {
    $queryVoda = "SELECT "
            . " SUM(IF(aggiuntaVodafone.fasePDA='OK' AND vodafone.idGestioneLead NOT LIKE 'GCL%',1,0)) as 'OKOUT' "
            . " FROM "
            . " `vodafone` "
            . " inner join aggiuntaVodafone on aggiuntaVodafone.id=vodafone.id "
            . " where "
            . "  dataVendita  <='$_dataMaggioreOre' "
            . " and dataVendita  >='$_dataMinoreOre' "
            . " and creatoDa='$_operatore' "
            . " group by "
            . " creatoDa";

    $risultatoVoda = $_conn->query($queryVoda);
    if (($rigaVoda = $risultatoVoda->fetch_array())) {

        $okVodaOut = $rigaVoda[0];
//$convertitoVoda = $rigaVoda[3];
    } else {

        $okVodaOut = 0;
    }
    return $okVodaOut;
}

function chiamateRicevute($_conn, $_dataMinima, $_dataMassima, $_operatore) {
    $query = " select count(*) from vicidial_closer_log where call_date between '$_dataMinima' and '$_dataMassima' and user= '$_operatore'";
    $risultato = $_conn->query($query);
    if (($riga = $risultato->fetch_array())) {
        $numero = $riga[0];
    } else {
        $numero = 0;
    }
    return $numero;
}



function okTim($_conn, $_dataMaggiore, $_dataMinore) {
    $queryTim = "SELECT "
            . " SUM(IF(aggiuntaTim.fasePDA='OK',1,0)) as 'OK' "
            . " FROM "
            . " `tim` "
            . " inner join aggiuntaTim on aggiuntaTim.id=tim.id "
            . " left join gestioneLead on gestioneLead.idSponsorizzata=tim.idGestioneLead "
            . " where "
            . " idGestioneLead like 'GCL%' "
            . " and gestioneLead.dataImport <='$_dataMaggiore' "
            . " and gestioneLead.dataImport >='$_dataMinore' ";

    $risultatoTim = $_conn->query($queryTim);
    if (($rigaTim = $risultatoTim->fetch_array())) {
        $okTim = $rigaTim[0];
    } else {
        $okTim = 0;
    }
    return $okTim;
}

function okTimOperatore($_conn, $_dataMaggiore, $_dataMinore, $_operatore) {
    $queryTim = "SELECT "
            . " SUM(IF(aggiuntaTim.fasePDA='OK' AND tim.idGestioneLead LIKE 'GCL%',1,0)) as 'OKIN' "
            . " FROM "
            . " `tim` "
            . " inner join aggiuntaTim on aggiuntaTim.id=tim.id "
            . " left join gestioneLead on gestioneLead.idSponsorizzata=tim.idGestioneLead "
            . " where "
            . "  gestioneLead.dataImport <='$_dataMaggiore' "
            . " and gestioneLead.dataImport >='$_dataMinore' "
            . " and creatoDa='$_operatore' "
            . " group by "
            . " creatoDa";

    $risultatoTim = $_conn->query($queryTim);
    if (($rigaTim = $risultatoTim->fetch_array())) {
        $okTim = $rigaTim[0];
    } else {
        $okTim = 0;
    }
    return $okTim;
}

function okOutTimOperatore($_conn, $_dataMaggioreOre, $_dataMinoreOre, $_operatore) {
    $queryTim = "SELECT "
            . " SUM(IF(aggiuntaTim.fasePDA='OK' AND tim.idGestioneLead NOT LIKE 'GCL%',1,0)) as 'OKOUT' "
            . " FROM "
            . " `tim` "
            . " inner join aggiuntaTim on aggiuntaTim.id=tim.id "
            . " where "
            . "  dataVendita  <='$_dataMaggioreOre' "
            . " and dataVendita  >='$_dataMinoreOre' "
            . " and creatoDa='$_operatore' "
            . " group by "
            . " creatoDa";

    $risultatoTim = $_conn->query($queryTim);
    if (($rigaTim = $risultatoTim->fetch_array())) {

        $okTimOut = $rigaTim[0];
//$convertitoVoda = $rigaVoda[3];
    } else {

        $okTimOut = 0;
    }
    return $okTimOut;
}

?>

