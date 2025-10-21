<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscallLead.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/siscall/script/funzioniAggiornamentoLivello.php";

$obj = new connessioneSiscallLead();
$conn = $obj->apriConnessioneSiscallLead();

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$oggi = date('N');
$ieri = date('N', strtotime('-1 days'));
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



    /**
     * Inizio Processo prelievo giornaliero Siscall1
     */
    $dataImport = date('Y-m-d H:i:s');

    $provenienza = "siscall2";
    $numeroChiamate = 0;

    $riferimento = mediaRiferimento($conn19, $dataMaggiore, $dataMinore);
    $mediaRiferimento = $riferimento['mediaRiferimento'];
    $leadTotali = $riferimento['leadTotali'];
    $okTotali = $riferimento['okTotali'];
    echo $dataMaggiore." ".$dataMinore."<br>";
    echo $mediaRiferimento . "lead: " . $leadTotali . "- ok: " . $okTotali . "<br>";

    /**
     * Query per il calcolo del livello di ogni singolo operatore
     */
    $querySiscall = "Select full_name,user_group from vicidial_users "
            . " where "
            . " (user_group like 'INB_%' OR user_group like 'OP_%' OR user_group like 'Op_%') "
            // . " user_group='Op_Spa_aricciuto' "
            . " AND user_level<6";
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
            $okPleni = okPlenitudeOperatore($conn19, $dataMaggiore, $dataMinore, $operatore);
            $okEnel = okEnelOperatore($conn19, $dataMaggiore, $dataMinore, $operatore);
            $okPleniOut = okOutPlenitudeOperatore($conn19, $dataMaggioreOre, $dataMinoreOre, $operatore);
            $okEnelOut = okOutEnelOperatore($conn19, $dataMaggioreOre, $dataMinoreOre, $operatore);
            $okVivi = okVivigasOperatore($conn19, $dataMaggiore, $dataMinore, $operatore);
            $okViviOut = okOutVivigasOperatore($conn19, $dataMaggioreOre, $dataMinoreOre, $operatore);
            $okIren = okIrenOperatore($conn19, $dataMaggiore, $dataMinore, $operatore);
            $okIrenOut = okOuitIrenOperatore($conn19, $dataMaggiore, $dataMinore, $operatore);
            $okUnion = okUnionOperatore($conn19, $dataMaggiore, $dataMinore, $operatore);
            $okUnionOut = okOutUnionOperatore($conn19, $dataMaggiore, $dataMinore, $operatore);
            $okVoda = okVodafoneOperatore($conn19, $dataMaggiore, $dataMinore, $operatore);
            $okVodaOut = okOutVodafoneOperatore($conn19, $dataMaggioreOre, $dataMinoreOre, $operatore);
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



        echo $fullName . ": Media= " . $media . "| livello= " . $livello . " |lead: " . $convertiti . " |ok: " . $ok . "|ore Out:" . $oreOutbound . "|Ok Out: " . $okOut . "|Resa Out: " . $resaOutbound . "|Livello:" . $livello .  "<br>";
        $dataImport = date('Y-m-d H:i:s');
//        $numeroChiamate = 3;
//        $queryUpdate = "UPDATE `vicidial_users` SET user_level='$livello',email='$dataImport',max_inbound_calls=$numeroChiamate  WHERE full_name='$fullName' ";
//        $conn->query($queryUpdate);
    }
 



$obj->chiudiConnessioneSiscallLead();
$obj19->chiudiConnessione();
?>
