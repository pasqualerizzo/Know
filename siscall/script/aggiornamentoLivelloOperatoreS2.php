<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscallLead.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";

$obj = new connessioneSiscallLead();
$conn = $obj->apriConnessioneSiscallLead();

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();
/**
 * Inizio Processo prelievo giornaliero Siscall1
 */
$dataImport = date('Y-m-d H:i:s');
$oggi = date('N');
//$oggi = date('Y-m-d', strtotime('2023-01-01'));
$ieri = date('N', strtotime('-1 days'));

//$ieri = date('Y-m-d', strtotime('2023-04-04'));
$provenienza = "siscall2";
$numeroChiamate = 0;

if ($oggi > 3 and $oggi < 7) {
    $dataMaggiore = date("Y-m-d 23:59:59", strtotime("Wednesday this week"));
    $dataMinore = date("Y-m-d 00:00:00", strtotime("monday this week "));
    $dataMaggioreOre = date("Y-m-d", strtotime("Wednesday this week"));
    $dataMinoreOre = date("Y-m-d", strtotime("monday this week "));
} else {
    $dataMaggiore = date("Y-m-d 23:59:59", strtotime("Saturday  previous week"));
    $dataMinore = date("Y-m-d 00:00:00", strtotime("Thursday previous week "));
    $dataMaggioreOre = date("Y-m-d", strtotime("Saturday  previous week"));
    $dataMinoreOre = date("Y-m-d", strtotime("Thursday previous week "));
}
//echo $dataMaggiore . "-" . $dataMinore . "<br>";
//if($ieri<>7){
//$dataMaggiore=date("Y-m-d 23:59:59", strtotime("-1 day"));
//$dataMinore=date("Y-m-d 00:00:00", strtotime("-1 day"));
//}else{
//    $dataMaggiore=date("Y-m-d 23:59:59", strtotime("-2 day"));
//$dataMinore=date("Y-m-d 00:00:00", strtotime("-2 day"));
//}

$queryTotale = "SELECT"
        . " COUNT(`idSponsorizzata`) AS Lead "
        . " FROM "
        . " `gestioneLead` "
        . " WHERE "
        . " `CategoriaUltima` NOT IN ('NONUTILICHIUSI') "
        . " AND dataImport <= '$dataMaggiore' "
        . " AND dataImport >= '$dataMinore' "
        . " and idSponsorizzata like 'GCL%' "
        . " and (duplicato='no' or duplicato='')";

$risultatoTotale = $conn19->query($queryTotale);
$rigaTotale = $risultatoTotale->fetch_array();
$leadTotali = $rigaTotale[0];

/**
 * 
 */
$queryPleni = "SELECT "
        . " SUM(IF(aggiuntaPlenitude.fasePDA='OK',1,0)) as 'OK' "
        . " FROM "
        . " `plenitude` "
        . " inner join aggiuntaPlenitude on aggiuntaPlenitude.id=plenitude.id "
        . " left join gestioneLead on gestioneLead.idSponsorizzata=plenitude.idGestioneLead "
        . " where "
        . " idGestioneLead like 'GCL%' "
        . " and gestioneLead.dataImport <='$dataMaggiore' "
        . " and gestioneLead.dataImport >='$dataMinore' ";

//echo $queryPleni;

$risultatoPleni = $conn19->query($queryPleni);
if (($rigaPleni = $risultatoPleni->fetch_array())) {
    $okPleni = $rigaPleni[0];
} else {
    $okPleni = 0;
}


/**
 * Enel 
 */
$queryEnel = "SELECT "
        . " SUM(IF(aggiuntaEnel.fasePDA='OK',1,0)) as 'OK' "
        . " FROM "
        . " `enel` "
        . " inner join aggiuntaEnel on aggiuntaEnel.id=enel.id "
        . " left join gestioneLead on gestioneLead.idSponsorizzata=enel.idGestioneLead "
        . " where "
        . " idGestioneLead like 'GCL%' "
        . " and gestioneLead.dataImport <='$dataMaggiore' "
        . " and gestioneLead.dataImport >='$dataMinore' ";

//echo $queryPleni;

$risultatoEnel = $conn19->query($queryEnel);
if (($rigaEnel = $risultatoEnel->fetch_array())) {
    $okEnel = $rigaEnel[0];
} else {
    $okEnel = 0;
}

$queryvivigas = "SELECT "
        . " SUM(IF(aggiuntaVivigas.fasePDA='OK',1,0)) as 'OK' "
        . " FROM "
        . " `vivigas` "
        . " inner join aggiuntaVivigas on aggiuntaVivigas.id=vivigas.id "
        . " left join gestioneLead on gestioneLead.idSponsorizzata=vivigas.idGestioneLead "
        . " where "
        . " idGestioneLead like 'GCL%' "
        . " and gestioneLead.dataImport <='$dataMaggiore' "
        . " and gestioneLead.dataImport >='$dataMinore' ";

$risultatoVivi = $conn19->query($queryvivigas);
if (($rigaVivi = $risultatoVivi->fetch_array())) {
    $okVivi = $rigaVivi[0];
} else {
    $okVivi = 0;
}

$queryIren = "SELECT "
        . " SUM(IF(aggiuntaIren.fasePDA='OK',1,0)) as 'OK' "
        . " FROM "
        . " `iren` "
        . " inner join aggiuntaIren on aggiuntaIren.id=iren.id "
        . " left join gestioneLead on gestioneLead.idSponsorizzata=iren.idGestioneLead "
        . " where "
        . " idGestioneLead like 'GCL%' "
        . " and gestioneLead.dataImport <='$dataMaggiore' "
        . " and gestioneLead.dataImport >='$dataMinore' ";

$risultatoIren = $conn19->query($queryIren);
if (($rigaIren = $risultatoIren->fetch_array())) {
    $okIren = $rigaIren[0];
} else {
    $okIren = 0;
}

$queryUnion = "SELECT "
        . " SUM(IF(aggiuntaUnion.fasePDA='OK',1,0)) as 'OK' "
        . " FROM "
        . " `union` "
        . " inner join aggiuntaUnion on aggiuntaUnion.id=union.id "
        . " left join gestioneLead on gestioneLead.idSponsorizzata=union.idGestioneLead "
        . " where "
        . " idGestioneLead like 'GCL%' "
        . " and gestioneLead.dataImport <='$dataMaggiore' "
        . " and gestioneLead.dataImport >='$dataMinore' ";

$risultatoUnion = $conn19->query($queryUnion);
if (($rigaUnion = $risultatoUnion->fetch_array())) {
    $okUnion = $rigaUnion[0];
} else {
    $okUnion = 0;
}
$queryVoda = "SELECT "
        . " SUM(IF(aggiuntaVodafone.fasePDA='OK',1,0)) as 'OK' "
        . " FROM "
        . " `vodafone` "
        . " inner join aggiuntaVodafone on aggiuntaVodafone.id=vodafone.id "
        . " left join gestioneLead on gestioneLead.idSponsorizzata=vodafone.idGestioneLead "
        . " where "
        . " idGestioneLead like 'GCL%' "
        . " and gestioneLead.dataImport <='$dataMaggiore' "
        . " and gestioneLead.dataImport >='$dataMinore' ";

$risultatoVoda = $conn19->query($queryVoda);
if (($rigaVoda = $risultatoVoda->fetch_array())) {
    $okVoda = $rigaVoda[0];
} else {
    $okVoda = 0;
}
$okTotali = $okPleni + $okVivi + $okIren + $okUnion + $okVoda + $okEnel;
$mediaRiferimento = ($leadTotali == 0) ? 0 : round(($okTotali / $leadTotali) * 100, 2);
echo $mediaRiferimento . "lead: " . $leadTotali . "- ok: " . "$okTotali" . "<br>";

/**
 * Query per il calcolo del livello di ogni singolo operatore
 */
$querySiscall = "Select full_name,user_group from vicidial_users where (user_group like 'INB_%' OR user_group like 'OP_%' OR user_group like 'Op_%') AND user_level<6";
$risultatoSiscall = $conn->query($querySiscall);
$i = 0;

while ($rigaSiscall = $risultatoSiscall->fetch_array()) {
    $media = 0;
    $lead = 0;
    $ok = 0;
    $oreOutbound = 0;
    $resaOutbound = 0;
    $okOut = 0;
    $convertiti = 0;
    $fullName = $rigaSiscall[0];
    $userGroup = $rigaSiscall[1];
    $i++;
    $query = "SELECT"
            . " REPLACE(`gestitoDa`, 'enel', '') AS operatore, "
            . " COUNT(`idSponsorizzata`) AS Lead, "
            . " SUM(if(duplicato='no',1,0)) AS TOT_CP "
            . " FROM "
            . " `gestioneLead` "
            . " WHERE "
            . " `CategoriaUltima` NOT IN ('NONUTILICHIUSI') "
            . " AND dataImport <= '$dataMaggiore' "
            . " AND dataImport >= '$dataMinore' "
            . " and idSponsorizzata like 'GCL%'"
            . " and REPLACE(`gestitoDa`, 'enel', '') ='$fullName'"
            . " GROUP BY `gestitoDa` "
            . " ORDER BY `valoreMedioIren` ASC";
    $risultato = $conn19->query($query);
    if (($risultato->num_rows) == 0) {
        if ($userGroup == 'INB_BMP') {
            $livello = 2;
            $numeroChiamate = 15;
            $convertiti = 0;
        } else {
            $livello = 1;
            $numeroChiamate = 10;
            $convertiti = 0;
        }
    } else {
        $numeroChiamate = 0;
        $riga = $risultato->fetch_array();
        $operatore = $riga[0];
        $lead = $riga[1];
        $convertiti = $riga[2];

        /**
         * Query per il prelievo dei pezzi fatti dall'operatore 
         */
        $queryPleni = "SELECT "
                . " count(aggiuntaPlenitude.fasePDA) AS 'OKIN' "
                . " FROM "
                . " `plenitude` "
                . " inner join aggiuntaPlenitude on aggiuntaPlenitude.id=plenitude.id "
                . " left join gestioneLead on gestioneLead.idSponsorizzata=plenitude.idGestioneLead "
                . " where "
                . "  gestioneLead.dataImport <='$dataMaggiore' "
                . " and gestioneLead.dataImport >='$dataMinore' "
                . " and creatoDa='$operatore' "
                . " and aggiuntaPlenitude.fasePDA = 'OK' "
                . " and plenitude.idGestioneLead LIKE 'GCL%' ";

        //echo $queryPleni;

        $risultatoPleni = $conn19->query($queryPleni);
        if (($rigaPleni = $risultatoPleni->fetch_array())) {
            $okPleni = $rigaPleni[0];
        } else {
            $okPleni = 0;
        }

        /**
         * Enel In
         */
        $queryEnel = "SELECT "
                . " count(aggiuntaEnel.fasePDA) AS 'OKIN' "
                . " FROM "
                . " `enel` "
                . " inner join aggiuntaEnel on aggiuntaEnel.id=enel.id "
                . " left join gestioneLead on gestioneLead.idSponsorizzata=enel.idGestioneLead "
                . " where "
                . "  gestioneLead.dataImport <='$dataMaggiore' "
                . " and gestioneLead.dataImport >='$dataMinore' "
                . " and creatoDa='$operatore' "
                . " and aggiuntaEnel.fasePDA = 'OK' "
                . " and enel.idGestioneLead LIKE 'GCL%'";

        //echo $queryEnel;

        $risultatoEnel = $conn19->query($queryEnel);
        if (($rigaEnel = $risultatoEnel->fetch_array())) {
            $okEnel = $rigaEnel[0];
            //echo $okEnel;
        } else {
            $okEnel = 0;
        }


        /**
         * Query pleni out
         */
        $queryPleni = "SELECT "
                . " SUM(IF(aggiuntaPlenitude.fasePDA = 'OK' AND plenitude.idGestioneLead NOT LIKE 'GCL%',1,0)) AS 'OKOUT' "
                . " FROM "
                . " `plenitude` "
                . " inner join aggiuntaPlenitude on aggiuntaPlenitude.id=plenitude.id "
                . " where "
                . "  data <='$dataMaggioreOre' "
                . " and data >='$dataMinoreOre' "
                . " and creatoDa='$operatore' "
                . " group by "
                . " creatoDa";

        //echo $queryPleni;

        $risultatoPleni = $conn19->query($queryPleni);
        if (($rigaPleni = $risultatoPleni->fetch_array())) {

            $okPleniOut = $rigaPleni[0];
        } else {

            $okPleniOut = 0;
        }

        /**
         * Enel Out 
         */
        $queryEnelO = "SELECT "
                . " SUM(IF(aggiuntaEnel.fasePDA = 'OK' AND enel.idGestioneLead NOT LIKE 'GCL%',1,0)) AS 'OKOUT' "
                . " FROM "
                . " `enel` "
                . " inner join aggiuntaEnel on aggiuntaEnel.id=enel.id "
                . " where "
                . "  data <='$dataMaggioreOre' "
                . " and data >='$dataMinoreOre' "
                . " and creatoDa='$operatore' "
                . " group by "
                . " creatoDa";

        //echo $queryPleni;

        $risultatoEnelO = $conn19->query($queryEnelO);
        if (($rigaEnelO = $risultatoEnelO->fetch_array())) {

            $okEnelOut = $rigaEnelO[0];
        } else {

            $okEnelOut = 0;
        }


        /**
         * Query vivi in
         */
        $queryvivigas = "SELECT "
                . " SUM(IF(aggiuntaVivigas.fasePDA='OK' AND vivigas.idGestioneLead LIKE 'GCL%',1,0)) as 'OKIN' "
                . " FROM "
                . " `vivigas` "
                . " inner join aggiuntaVivigas on aggiuntaVivigas.id=vivigas.id "
                . " left join gestioneLead on gestioneLead.idSponsorizzata=vivigas.idGestioneLead "
                . " where "
                . "  gestioneLead.dataImport <='$dataMaggiore' "
                . " and gestioneLead.dataImport >='$dataMinore' "
                . " and creatoDa='$operatore' "
                . " group by "
                . " creatoDa";

        $risultatoVivi = $conn19->query($queryvivigas);
        if (($rigaVivi = $risultatoVivi->fetch_array())) {
            $okVivi = $rigaVivi[0];
        } else {
            $okVivi = 0;
        }
        //echo $queryvivigas;

        /**
         * Query vivi out
         */
        $queryvivigas = "SELECT "
                . " SUM(IF(aggiuntaVivigas.fasePDA='OK' AND vivigas.idGestioneLead NOT LIKE 'GCL%',1,0)) as 'OKOUT' "
                . " FROM "
                . " `vivigas` "
                . " inner join aggiuntaVivigas on aggiuntaVivigas.id=vivigas.id "
                . " where "
                . "  data <='$dataMaggioreOre' "
                . " and data >='$dataMinoreOre' "
                . " and creatoDa='$operatore' "
                . " group by "
                . " creatoDa";

        $risultatoVivi = $conn19->query($queryvivigas);
        if (($rigaVivi = $risultatoVivi->fetch_array())) {

            $okViviOut = $rigaVivi[0];
        } else {

            $okViviOut = 0;
        }
        /**
         * Query iren in
         */
        $queryIren = "SELECT "
                . " SUM(IF(aggiuntaIren.fasePDA='OK' AND iren.idGestioneLead LIKE 'GCL%',1,0)) as 'OKIN' "
                . " FROM "
                . " `iren` "
                . " inner join aggiuntaIren on aggiuntaIren.id=iren.id "
                . " left join gestioneLead on gestioneLead.idSponsorizzata=iren.idGestioneLead "
                . " where "
                . "  gestioneLead.dataImport <='$dataMaggiore' "
                . " and gestioneLead.dataImport >='$dataMinore' "
                . " and creatoDa='$operatore' "
                . " group by "
                . " creatoDa";

        $risultatoIren = $conn19->query($queryIren);
        if (($rigaIren = $risultatoIren->fetch_array())) {
            $okIren = $rigaIren[0];
        } else {
            $okIren = 0;
        }

        //echo $queryIren;
        /**
         * query iren out
         */
        $queryIren = "SELECT "
                . " SUM(IF(aggiuntaIren.fasePDA='OK' AND iren.idGestioneLead NOT LIKE 'GCL%',1,0)) as 'OKOUT' "
                . " FROM "
                . " `iren` "
                . " inner join aggiuntaIren on aggiuntaIren.id=iren.id "
                . " where "
                . "  data <='$dataMaggiore' "
                . " and data >='$dataMinore' "
                . " and creatoDa='$operatore' "
                . " group by "
                . " creatoDa";

        $risultatoIren = $conn19->query($queryIren);
        if (($rigaIren = $risultatoIren->fetch_array())) {

            $okIrenOut = $rigaIren[0];
        } else {

            $okIrenOut = 0;
        }
        /**
         * Query union in
         */
        $queryUnion = "SELECT "
                . " SUM(IF(aggiuntaUnion.fasePDA='OK' AND union.idGestioneLead LIKE 'GCL%',1,0)) as 'OKIN' "
                . " FROM "
                . " `union` "
                . " inner join aggiuntaUnion on aggiuntaUnion.id=union.id "
                . " left join gestioneLead on gestioneLead.idSponsorizzata=union.idGestioneLead "
                . " where "
                . "  gestioneLead.dataImport <='$dataMaggiore' "
                . " and gestioneLead.dataImport >='$dataMinore' "
                . " and creatoDa='$operatore' "
                . " group by "
                . " creatoDa";

        $risultatoUnion = $conn19->query($queryUnion);
        if (($rigaUnion = $risultatoUnion->fetch_array())) {
            $okUnion = $rigaUnion[0];
        } else {
            $okUnion = 0;
        }
        //echo $queryUnion;
        /**
         * Query union out
         */
        $queryUnion = "SELECT "
                . " SUM(IF(aggiuntaUnion.fasePDA='OK' AND union.idGestioneLead NOT LIKE 'GCL%',1,0)) as 'OKOUT' "
                . " FROM "
                . " `union` "
                . " inner join aggiuntaUnion on aggiuntaUnion.id=union.id "
                . " where "
                . " data <='$dataMaggiore' "
                . " and data >='$dataMinore' "
                . " and creatoDa='$operatore' "
                . " group by "
                . " creatoDa";

        $risultatoUnion = $conn19->query($queryUnion);
        if (($rigaUnion = $risultatoUnion->fetch_array())) {

            $okUnionOut = $rigaUnion[0];
        } else {

            $okUnionOut = 0;
        }
        /**
         * query voda
         */
        $queryVoda = "SELECT "
                . " SUM(IF(aggiuntaVodafone.fasePDA='OK' AND vodafone.idGestioneLead NOT LIKE 'GCL%',1,0)) as 'OKOUT' "
                . " FROM "
                . " `vodafone` "
                . " inner join aggiuntaVodafone on aggiuntaVodafone.id=vodafone.id "
                . " where "
                . "  dataVendita  <='$dataMaggioreOre' "
                . " and dataVendita  >='$dataMinoreOre' "
                . " and creatoDa='$operatore' "
                . " group by "
                . " creatoDa";

        $risultatoVoda = $conn19->query($queryVoda);
        if (($rigaVoda = $risultatoVoda->fetch_array())) {

            $okVodaOut = $rigaVoda[0];
            //$convertitoVoda = $rigaVoda[3];
        } else {

            $okVodaOut = 0;
        }

        /**
         * query voda
         */
        $queryVoda = "SELECT "
                . " SUM(IF(aggiuntaVodafone.fasePDA='OK' AND vodafone.idGestioneLead LIKE 'GCL%',1,0)) as 'OKIN' "
                . " FROM "
                . " `vodafone` "
                . " inner join aggiuntaVodafone on aggiuntaVodafone.id=vodafone.id "
                . " left join gestioneLead on gestioneLead.idSponsorizzata=vodafone.idGestioneLead "
                . " where "
                . "  gestioneLead.dataImport <='$dataMaggiore' "
                . " and gestioneLead.dataImport >='$dataMinore' "
                . " and creatoDa='$operatore' "
                . " group by "
                . " creatoDa";

        $risultatoVoda = $conn19->query($queryVoda);
        if (($rigaVoda = $risultatoVoda->fetch_array())) {
            $okVoda = $rigaVoda[0];
        } else {
            $okVoda = 0;
        }

        $ok = $okPleni + $okVivi + $okIren + $okUnion + $okVoda + $okEnel;
        $okOut = $okPleniOut + $okViviOut + $okIrenOut + $okUnionOut + $okVodaOut + $okEnelOut;
        $media = ($convertiti == 0) ? 0 : round(($ok / $convertiti) * 100, 2);
        $differenza = $media - $mediaRiferimento;
        if ($userGroup == 'INB_BMP') {
            $livello = 2;
            $numeroChiamate = 15;
        } else {

            if ($differenza <= -5) {
                $livello = 1;
                $numeroChiamate = 10;
            } elseif ($differenza > -5 and $differenza <= 0) {
                $livello = 2;
                $numeroChiamate = 15;
            } elseif ($differenza > 0 and $differenza <= 5) {
                $livello = 3;
                $numeroChiamate = 20;
            } elseif ($differenza > 5 and $differenza <= 10) {
                $livello = 4;
                $numeroChiamate = 0;
            } elseif ($differenza > 10) {
                $livello = 5;
                $numeroChiamate = 0;
            }



            if ($livello < 3) {
                $queryOreTot = "SELECT"
                        . " SUM(numero)/3600 as ore "
                        . " FROM "
                        . " `stringheTotale` "
                        . " where "
                        . " giorno<='$dataMaggioreOre' "
                        . " AND giorno>='$dataMinoreOre' "
                        . " AND nomeCompleto='$operatore'";
                $risultatoOreTotale = $conn19->query($queryOreTot);
                if ($risultatoOreTotale->num_rows > 0) {
                    $rigaOreTotale = $risultatoOreTotale->fetch_array();
                    $oreTotale = $rigaOreTotale[0];
                } else {
                    $oreTotale = 0;
                }

                /**
                 * Se il livello l'operatore è minore a 3 
                 */
                $queryOreInb = "SELECT"
                        . " SUM(numero)/3600 as ore "
                        . " FROM "
                        . " `stringheSiscallLead` "
                        . " where "
                        . " giorno<='$dataMaggioreOre' "
                        . " AND giorno>='$dataMinoreOre' "
                        . " AND nomeCompleto='$operatore' "
                        . " AND mandato='Lead Inbound'";
                $risultatoOreIn = $conn19->query($queryOreInb);
                if ($risultatoOreIn->num_rows > 0) {
                    $rigaOreIn = $risultatoOreIn->fetch_array();
                    $oreInbound = $rigaOreIn[0];
                } else {
                    $oreInbound = 0;
                }

                $oreOutbound = $oreTotale - $oreInbound;
                $resaOutbound = ($oreOutbound == 0) ? 0 : round(($okOut / $oreOutbound), 2);
                if ($resaOutbound > 0.2) {
                    $livello = 3;
                    $numeroChiamate = 20;
                } else {
                    
                }
            }
        }
    }



    echo $fullName . ": Media= " . $media . "| livello= " . $livello . " |lead: " . $convertiti . " |ok: " . $ok . "|ore Out:" . $oreOutbound . "|Ok Out: " . $okOut . "|Resa Out: " . $resaOutbound . "|Livello:" . $livello . "Numero Chiamate:" . $numeroChiamate . "<br>";
    $dataImport = date('Y-m-d H:i:s');
    $queryUpdate = "UPDATE `vicidial_users` SET user_level='$livello',email='$dataImport',max_inbound_calls=$numeroChiamate  WHERE full_name='$fullName' ";
    $conn->query($queryUpdate);
}
$obj->chiudiConnessioneSiscallLead();
$obj19->chiudiConnessione();


    