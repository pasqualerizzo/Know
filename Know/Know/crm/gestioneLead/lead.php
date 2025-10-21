<?php

$queryRicerca = "SELECT "
        . "gl.gestioneleadno as idSponsorizzate, "
        . "glCF.cf_4044 as nome, "
        . "glCF.cf_4046 as cognome, "
        . "glCF.cf_4048 as mail, "
        . "glCF.cf_4050 as UTMc, "
        . "glCF.cf_4052 as UTMm, "
        . "glCF.cf_4054 as UTMs, "
        . "glCF.cf_4056 as ip, "
        . "glCF.cf_4058 as 'data import', "
        . "glCF.cf_4060 as origine, "
        . "glCF.cf_4062 as brand, "
        . "glCF.cf_4066 as leadId, "
        . "glCF.cf_4068 as source, "
        . "glCF.cf_4152 as categoraiaEsitoPrima, "
        . "glCF.cf_4156 as categoriaEsitoUltima, "
        . "glCF.cf_4112 as operatoreUltima "
        . "FROM "
        . "vtiger_gestionelead as gl "
        . "inner join vtiger_gestioneleadcf as glCF on glCF.gestioneleadid=gl.gestioneleadid "
        . "inner join vtiger_crmentity as e on glCF.gestioneleadid=e.crmid "
        . "where "
        . " e.deleted=0"
        . " AND glCF.cf_4058>='$confrontoCRM' ";

//echo $queryRicerca;
$risultato = $connCrm->query($queryRicerca);
/**
 * Se la ricerca da errore segna nei log l'errore
 */
if (!$risultato) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $connCrm->real_escape_string($connCrm->error);
    $queryErroreLog = "INSERT INTO `logImport`(`datImport`, `provenienza`, `descrizione`,stato,idStato) VALUES ('$dataErrore','$provenienza','$errore',0,'$idStato')";
    $conn19->query($queryErroreLog);
}
/**
 * se non da errore svuota la tabella, segnando nei log l'operazione, e la ricarica riga per riga da zero
 */ else {
    $queryTruncate = "DELETE FROM `gestioneLead` where dataImport>='$confrontoCRM' "
        . " AND idSponsorizzata like 'GE%'";
    $conn19->query($queryTruncate);

    $dataTruncate = date('Y-m-d H:i:s');
    $queryTruncateLog = "INSERT INTO `logImport`(`datImport`, `provenienza`, `descrizione`,stato,idStato) VALUES ('$dataTruncate','$provenienza','Truncate $provenienza',0,'$idStato')";
    $conn19->query($queryTruncateLog);

    while ($riga = $risultato->fetch_array()) {
        $somma = 0;
        $idSponsorizzata = $riga[0];
        $nome = $conn19->real_escape_string($riga[1]);
        $cognome = $conn19->real_escape_string($riga[2]);
        $mail = $riga[3];
        $utmC = $riga[4];
        $utmM = $riga[5];
        $utmS = $riga[6];
        $ip = $riga[7];
        $important = $riga[8];
        $origine = $riga[9];
        $brand = $riga[10];
        $leadId = $riga[11];
        $source = $riga[12];
        $categoriaEsitoPrima = $riga[13];

        //$categoriaEsitoUltima = $riga[14];

        $categoriaEsitoUltima = ($riga[14] == "") ? "BACKLOG" : $riga[14];
        $operatoreUltima = $riga[15];

        $meseRicerca = date('Y-m-01', strtotime($important));
        //echo $meseRicerca . "<br>";

        /**
         * Aggiornamento del 11/04/2024
         */
        switch ($origine) {
            case "AdviceMe":
                $agenzia = "AdviceMe";
                $utmC = $brand;
                $source = $brand;
                break;
            case "Energy":
            case "Telco":
            case "in_EnergyDiretto":
            case "in_Telco":
                $agenzia = "Muza";
                break;
            case "entrambe":
            case "entrambi":

            case "bolletta_luce/gas":
            case "bolletta luce":
            case "bolletta luce/gas":
            case "Amazon":
            case "telefonia":
            case "Telefonia":
            case "in_Energy":
            case "Gas":
            case "Luce":
                if ($dataImport < "2024-06-11") {
                    $agenzia = "Arkys";
                } else {
                    $agenzia = "NovaMarketing";
                }
                break;
            case "Misto":
                if ($dataImport < "2024-06-11") {
                    $agenzia = "Arkys";
                } else {
                    $agenzia = "NovaMarketing";
                }
                if ($utmC == "Sito" || $utmC == "Vuoto") {
                    $source = "Sito";
                }

                break;

            case "Sito":
                if ($dataImport < "2024-06-11") {
                    $agenzia = "Arkys";
                } else {
                    $agenzia = "NovaMarketing";
                }
                $source = "Sito";
                //$agenzia = "Risparmiami.it";
                break;
            case "Store":
                $agenzia = "VodafoneStore.it";
                break;
            default :
                echo $origine;
                echo "<br>";
                break;
        }

        switch ($source) {
            case "fb":
            case "ig":
            case "nn":
            case "block":
            case "Facebook":
            case "":
            case "vuoto":
                $source = "Meta";
                break;
        }



        /**
         * Plenitude
         */
        $queryCrm = "SELECT "
                . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)),data "
                . "FROM "
                . "plenitude "
                . "inner JOIN aggiuntaPlenitude on plenitude.id=aggiuntaPlenitude.id "
                . "WHERE "
                . " statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco' and idGestioneLead='$idSponsorizzata'";
        //echo $queryCrm;
        //echo "<br>";
        $risultatoPleni = $conn19->query($queryCrm);
        $queryMediaP = "SELECT media FROM `mediaPraticaMese` where mandato='Plenitude' and mese='$meseRicerca'";
        //echo $queryMedia;
        $risultatoMediaP = $conn19->query($queryMediaP);
        $conteggioP = $risultatoMediaP->num_rows;
        if ($conteggioP > 0) {
            $rigaMediaP = $risultatoMediaP->fetch_array();
            $mediaPleni = $rigaMediaP[0];
        } else {
            $mediaPleni = 0;
        }
        while ($rigaPleni = $risultatoPleni->fetch_array()) {
            $pleniTot = (($rigaPleni[0] == null) ? 0 : $rigaPleni[0]);
            $pleniOk = (($rigaPleni[1] == null) ? 0 : $rigaPleni[1]);
            $pleniKo = (($rigaPleni[2] == null) ? 0 : $rigaPleni[2]);
            $valoreMedioPleni = $pleniOk * $mediaPleni;
            $dataContrattoPleni = (($rigaPleni[3] == null) ? '0000-00-00' : $rigaPleni[3]);
        }
        /**
         * Iren
         */
        $queryCrm = "SELECT "
                . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)),data "
                . "FROM "
                . "iren "
                . "inner JOIN aggiuntaIren on iren.id=aggiuntaIren.id "
                . "WHERE "
                . " statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco'  and idGestioneLead='$idSponsorizzata'";
        //echo $queryCrm;
        //echo "<br>";
        $risultatoIren = $conn19->query($queryCrm);
        $queryMediaP = "SELECT media FROM `mediaPraticaMese` where mandato='Iren' and mese='$meseRicerca'";
        //echo $queryMedia;
        $risultatoMediaP = $conn19->query($queryMediaP);
        $conteggioP = $risultatoMediaP->num_rows;
        if ($conteggioP > 0) {
            $rigaMediaP = $risultatoMediaP->fetch_array();
            $mediaIren = $rigaMediaP[0];
        } else {
            $mediaIren = 0;
        }
        while ($rigaIren = $risultatoIren->fetch_array()) {
            $irenTot = (($rigaIren[0] == null) ? 0 : $rigaIren[0]);
            $irenOk = (($rigaIren[1] == null) ? 0 : $rigaIren[1]);
            $irenKo = (($rigaIren[2] == null) ? 0 : $rigaIren[2]);
            $valoreMedioIren = $irenOk * $mediaIren;
            $dataContrattoIren = (($rigaIren[3] == null || $rigaIren[3] == 0) ? '0000-00-00' : $rigaIren[3]);
        }


        /**
         * Union
         */
        $queryCrm = "SELECT "
                . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)),data "
                . "FROM "
                . "know.union "
                . "inner JOIN aggiuntaUnion on know.union.id=aggiuntaUnion.id "
                . "WHERE "
                . " statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco'  and idGestioneLead='$idSponsorizzata'";
        //echo $queryCrm;
        //echo "<br>";
        $risultatoUnion = $conn19->query($queryCrm);
        $queryMediaP = "SELECT media FROM `mediaPraticaMese` where mandato='union' and mese='$meseRicerca'";
        //echo $queryMedia;
        $risultatoMediaP = $conn19->query($queryMediaP);
        $conteggioP = $risultatoMediaP->num_rows;
        if ($conteggioP > 0) {
            $rigaMediaP = $risultatoMediaP->fetch_array();
            $mediaUnion = $rigaMediaP[0];
        } else {
            $mediaUnion = 0;
        }
        while ($rigaUnion = $risultatoUnion->fetch_array()) {
            $unionTot = (($rigaUnion[0] == null) ? 0 : $rigaUnion[0]);
            $unionOk = (($rigaUnion[1] == null) ? 0 : $rigaUnion[1]);
            $unionKo = (($rigaUnion[2] == null) ? 0 : $rigaUnion[2]);
            $valoreMedioUnion = $unionOk * $mediaUnion;
            $dataContrattoUnion = (($rigaUnion[3] == null || $rigaUnion[3] == 0) ? '0000-00-00' : $rigaUnion[3]);
        }

        /**
         * vivigas
         */
        $queryMediaV = "SELECT media FROM `mediaPraticaMese` where mandato='Vivigas' and mese='$meseRicerca'";
        $risultatoMediaV = $conn19->query($queryMediaV);
        $conteggioV = $risultatoMediaV->num_rows;
        if ($conteggioV > 0) {
            $rigaMediaV = $risultatoMediaV->fetch_array();
            $mediaVivi = $rigaMediaV[0];
        } else {
            $mediaVivi = 0;
        }
        $queryCrmVivigas = "SELECT "
                . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)),data "
                . "FROM "
                . "vivigas "
                . "inner JOIN aggiuntaVivigas on vivigas.id=aggiuntaVivigas.id "
                . "WHERE "
                . " statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco'  and idGestioneLead='$idSponsorizzata'";
        //echo $queryCrmVodafone;
        $risultatoVivigas = $conn19->query($queryCrmVivigas);
        while ($rigaVivigas = $risultatoVivigas->fetch_array()) {
            $viviTot = (($rigaVivigas[0] == null) ? 0 : $rigaVivigas[0]);
            $viviOk = (($rigaVivigas[1] == null) ? 0 : $rigaVivigas[1]);
            $viviKo = (($rigaVivigas[2] == null) ? 0 : $rigaVivigas[2]);
            $valoreMediaVivi = $viviOk * $mediaVivi;
            $dataContrattoVivi = (($rigaVivigas[3] == null) ? '0000-00-00' : $rigaVivigas[3]);
        }
        /**
         * vodafone
         */
        $queryMediaVo = "SELECT media FROM `mediaPraticaMese` where mandato='Vodafone' and mese='$meseRicerca'";
        $risultatoMediaVo = $conn19->query($queryMediaVo);
        $conteggioVo = $risultatoMediaVo->num_rows;
        if ($conteggioVo > 0) {
            $rigaMediaVo = $risultatoMediaVo->fetch_array();
            $mediaVoda = $rigaMediaVo[0];
        } else {
            $mediaVoda = 0;
        }


        $queryCrmVodafone = "SELECT "
                . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)),dataVendita "
                . "FROM "
                . "vodafone "
                . "inner JOIN aggiuntaVodafone on vodafone.id=aggiuntaVodafone.id "
                . "WHERE "
                . " statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco'  and idGestioneLead='$idSponsorizzata'";
        //echo $queryCrmVodafone;
        $risultatoVodafone = $conn19->query($queryCrmVodafone);
        while ($rigaVodafone = $risultatoVodafone->fetch_array()) {
            $vodaTot = (($rigaVodafone[0] == null) ? 0 : $rigaVodafone[0]);
            $vodaOk = (($rigaVodafone[1] == null) ? 0 : $rigaVodafone[1]);
            $vodaKo = (($rigaVodafone[2] == null) ? 0 : $rigaVodafone[2]);
            $valoreMedioVoda = $vodaOk * $mediaVoda;
            $dataContrattoVoda = (($rigaVodafone[3] == null ) ? '0000-00-00' : $rigaVodafone[3]);
        }

        $somma = $pleniTot + $vodaTot + $viviTot + $irenTot + $unionTot;
        if ($somma > 0) {
            $convertito = 1;
        } else {
            $convertito = 0;
        }

        /**
         * Categoria
         */
        $categoria = "";
        $queryCategoria = "SELECT * FROM `categoriaCampagna` where utmCampagna ='$utmC'";
        $risultatoCategoria = $conn19->query($queryCategoria);
        $conteggioCategoria = $risultatoCategoria->num_rows;
        if ($conteggioCategoria == 0) {
            $queryInserimentoCategoria = "INSERT INTO `categoriaCampagna`( `utmCampagna`) VALUES ('$utmC')";
            $conn19->query($queryInserimentoCategoria);
            $idCategoria = $conn19->insert_id;
        } else {
            $rigaCategoria = $risultatoCategoria->fetch_array();
            $categoria = $rigaCategoria[2];
        }

        /**
         * Inseriemnto
         */
        $queryInserimento = "INSERT INTO `gestioneLead`"
                . "( `idSponsorizzata`, `nome`, `cognome`, `mail`, `utmCampagna`, `utmMedium`, `utmSource`, `ip`, `dataImport`, `origine`, `brand`, `leadId`, `source`,agenzia,pleniTot,pleniOk,pleniKo,vodaTot,vodaOk,vodaKo,viviTot,ViviOk,viviKo,irenTot,irenOk,irenKo,valoreMediaPleni,valoreMediaVivi,valoreMedioVoda,valoreMedioIren,categoriaPrima,CategoriaUltima,gestitoDa,dataContrattoPleni,dataContrattoIren,dataContrattoVivi,dataContrattoVoda,uniTot,uniOk,uniKo,valoreMedioUni,convertito,categoriaCampagna) "
                . "VALUES "
                . "('$idSponsorizzata','$nome','$cognome','$mail','$utmC','$utmM','$utmS','$ip','$important','$origine','$brand','$leadId','$source','$agenzia','$pleniTot','$pleniOk','$pleniKo','$vodaTot','$vodaOk','$vodaKo','$viviTot','$viviOk','$viviKo','$irenTot','$irenOk','$irenKo','$valoreMedioPleni','$valoreMediaVivi','$valoreMedioVoda','$valoreMedioIren','$categoriaEsitoPrima','$categoriaEsitoUltima','$operatoreUltima','$dataContrattoPleni','$dataContrattoIren','$dataContrattoVivi','$dataContrattoVoda','$unionTot','$unionOk','$unionKo','$valoreMedioUnion','$convertito','$categoria')";
        //echo $queryInserimento;
        //echo "<br>";



        $conn19->query($queryInserimento);
    }
}
?>
