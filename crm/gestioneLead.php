<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
/*
 * 23/01/2025 eliminato il limite della memoria
 */
ini_set('memory_limit', '-1');

error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "/Applications/MAMP/htdocs/Know/connessione/connessione_msqli_vici.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrm.php";

//$obj = new ConnessioneVici();
//$conn = $obj->apriConnessioneVici();

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$objCrm = new ConnessioneCrm();
$connCrm = $objCrm->apriConnessioneCrm();
/**
 * Inizio Processo prelievo giornaliero Siscall1
 */
$dataImport = date('Y-m-d H:i:s');
$oggi = date('Y-m-d');
$ieri = date('Y-m-d', strtotime('-1 days'));
$confrontoCRM = date('Y-m-1', strtotime('-3 months'));
$provenienza = "gestioneLead";
$tipoCampagna = "";

/**
 * Recupero valore idStato
 */
$queryIdStato = "SELECT max(idStato) FROM `logImport`";
$risultatoIdStato = $conn19->query($queryIdStato);
$rigaStato = $risultatoIdStato->fetch_array();
$idStato = $rigaStato[0] + 1;
/**
 * Query inserimento log iniziale
 */
$queryInzioLog = "INSERT INTO `logImport`(`datImport`, `provenienza`, `descrizione`,stato,idStato) VALUES ('$dataImport','$provenienza','Inizio Import da $provenienza',0,'$idStato')";
$conn19->query($queryInzioLog);
/**
 * Query ricerca sul crm2
 */
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
        . " e.deleted=0 and glCF.cf_4058>'2025-08-01'";

//echo $queryRicerca;
try {
    $risultato = $connCrm->query($queryRicerca);
} catch (Exception $e) {
    echo "Per il Valore:" . $idSponsorizzata . " = " . $e->getMessage() . "<br>";
}

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
    //$queryTruncate = "TRUNCATE TABLE `gestioneLead`";
    $queryTruncate = "DELETE FROM `gestioneLead` WHERE dataImport>='2025-08-01'";
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
        $sede = "";

//$categoriaEsitoUltima = $riga[14];

        $categoriaEsitoUltima = ($riga[14] == "") ? "BACKLOG" : $riga[14];
        $operatoreUltima = $riga[15];

        $meseRicerca = date('Y-m-01', strtotime($important));
//echo $meseRicerca . "<br>";

        /**
         * Aggiornamento del 11/04/2024
         */
        switch ($utmC) {
            case "Search_Call_Generale_10_06_2024":
            case "Search_Call_Nuovi_Allacci_Volture":
            case "Search_Call_Regioni_Nord_Italia":
            case "Search_Call_Brand":
            case "Search_Call_Brand_Comparatore":
            case "Search_Call_Telco_Fibra_internet":
            case "Search_Call_Telco_Fibra_internet_NS":
            case "Search_Call_Telco_Guasti_NS":
                $origine = "NovaDirect";
                break;

            case "Search_Call_NS_Nord_Italia_2":
            case "Search_Call_Brand_NS_30_09":
            case "Search_Call_Servizio_Clienti_10_07_24":
            case "Search_Call_NS_Allacci_Volture":


                $origine = "NovaStart";
                break;
            case "Meta_MQ_Eni_NativeForm":
                $agenzia = "ScegliAdesso";
                $origine = "ScegliAdesso";
                $source = "Meta";
        }


        switch ($origine) {
            case "NovaDirect":
                $agenzia = "NovaDirect";
                break;
            case "NovaDirectForm":
                $agenzia = "NovaDirectForm";
                break;
            case "NovaStart":
                $agenzia = "NovaStart";
                break;

            case "AdviceMe":
                $agenzia = "AdviceMe";
                $utmC = $brand;
                $source = $brand;
                break;
            case "dgtMedia":
                $agenzia = "DgtMedia";
                break;
            case "Energy":
            case "Telco":
            case "in_EnergyDiretto":
            case "in_Telco":
                $agenzia = "DgtMedia";
                break;
            case "entrambe":
            case "entrambi":
            case "lucegas_meta":
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
                } elseif ($dataImport < "2025-01-01") {
                    $agenzia = "NovaMarketing";
                } else {
                    $agenzia = "NovaDirect";
                }
                break;
            case "Misto":
                if ($dataImport < "2024-06-11") {
                    $agenzia = "Arkys";
                } elseif ($dataImport < "2025-01-01") {
                    $agenzia = "NovaMarketing";
                } else {
                    $agenzia = "NovaDirect";
                }
                if ($utmC == "Sito" || $utmC == "Vuoto") {
                    $source = "Sito";
                }
                break;

            case "Sito":
                if ($dataImport < "2024-06-11") {
                    $agenzia = "Arkys";
                } elseif ($dataImport < "2025-01-01") {
                    $agenzia = "NovaMarketing";
                } else {
                    $agenzia = "NovaDirect";
                }
                $source = "Sito";
//$agenzia = "Risparmiami.it";
                break;
            case "Store":
                $agenzia = "VodafoneStore.it";
                break;
            case "Muza":
                $agenzia = "DgtMedia";
                break;
            case "GTEnergie":
                $agenzia = "GTEnergie";
                break;
            default :
                //echo $origine;
                //echo "<br>";
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
                . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)),data,sum(if(fasePost='OK',pezzoLordo,0)) "
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
            $dataContrattoPleni = (($rigaPleni[5] == null) ? '0000-00-00' : $rigaPleni[5]);
            $pleniPostOk = (($rigaPleni[6] == null) ? 0 : $rigaPleni[6]);
        }


        /**
         * Plenitude
         */
        $queryCrm = "SELECT "
                . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)),data,sum(if(fasePost='OK',pezzoLordo,0)) "
                . "FROM "
                . "enel "
                . "inner JOIN aggiuntaEnel on enel.id=aggiuntaEnel.id "
                . "WHERE "
                . " statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco' and idGestioneLead='$idSponsorizzata'";
//echo $queryCrm;
//echo "<br>";
        $risultatoEnel = $conn19->query($queryCrm);
        $queryMediaP = "SELECT media FROM `mediaPraticaMese` where mandato='Enel' and mese='$meseRicerca'";
//echo $queryMedia;
        $risultatoMediaP = $conn19->query($queryMediaP);
        $conteggioP = $risultatoMediaP->num_rows;
        if ($conteggioP > 0) {
            $rigaMediaP = $risultatoMediaP->fetch_array();
            $mediaEnel = $rigaMediaP[0];
        } else {
            $mediaEnel = 0;
        }
        while ($rigaEnel = $risultatoEnel->fetch_array()) {
            $EnelTot = (($rigaEnel[0] == null) ? 0 : $rigaEnel[0]);
            $EnelOk = (($rigaEnel[1] == null) ? 0 : $rigaEnel[1]);
            $EnelKo = (($rigaEnel[2] == null) ? 0 : $rigaEnel[2]);
            $valoreMedioEnel = $EnelOk * $mediaEnel;
            $dataContrattoEnel = (($rigaEnel[5] == null) ? '0000-00-00' : $rigaEnel[5]);
            $EnePostlOk = (($rigaEnel[6] == null) ? 0 : $rigaEnel[6]);
        }


        /**
         * Iren
         */
        $queryCrm = "SELECT "
                . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)),data,sum(if(fasePost='OK',pezzoLordo,0)) "
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
            $dataContrattoIren = (($rigaIren[3] == null || $rigaIren[5] == 0) ? '0000-00-00' : $rigaIren[5]);
            $irenPostOk = (($rigaIren[6] == null) ? 0 : $rigaIren[6]);
        }


        /**
         * Union
         */
        $queryCrm = "SELECT "
                . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)),data,sum(if(fasePost='OK',pezzoLordo,0)) "
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
            $dataContrattoUnion = (($rigaUnion[5] == null || $rigaUnion[5] == 0) ? '0000-00-00' : $rigaUnion[5]);
            $unionPostOk = (($rigaUnion[6] == null) ? 0 : $rigaUnion[6]);
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
                . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)),data,sum(if(fasePost='OK',pezzoLordo,0)) "
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
            $dataContrattoVivi = (($rigaVivigas[5] == null) ? '0000-00-00' : $rigaVivigas[5]);
            $viviPostOk = (($rigaVivigas[6] == null) ? 0 : $rigaVivigas[6]);
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
            $dataContrattoVoda = (($rigaVodafone[5] == null) ? '0000-00-00' : $rigaVodafone[5]);
            $vodaPostOk = 0;
        }


        /**
         * Tim
         */
        $queryMediaTim = "SELECT media FROM `mediaPraticaMese` where mandato='Tim' and mese='$meseRicerca'";
        $risultatoMediaTim = $conn19->query($queryMediaTim);
        $conteggioTim = $risultatoMediaTim->num_rows;
        if ($conteggioTim > 0) {
            $rigaMediaTim = $risultatoMediaTim->fetch_array();
            $mediaTim = $rigaMediaTim[0];
        } else {
            $mediaTim = 0;
        }


        $queryCrmTim = "SELECT "
                . "sum(pezzoLordo),sum(if(fasePDA ='OK',pezzoLordo,0)),sum(if(fasePDA ='KO',pezzoLordo,0)), sum(if(fasePDA ='BKL',pezzoLordo,0)), sum(if(fasePDA ='BKLP',pezzoLordo,0)),dataVendita,sum(if(fasePost='OK',pezzoLordo,0)) "
                . "FROM "
                . "tim "
                . "inner JOIN aggiuntaTim on tim.id=aggiuntaTim.id "
                . "WHERE "
                . " statoPDA<>'bozza' and statoPDA<>'annullata' and statoPDA<>'pratica doppia' and statoPDA<>'In attesa Sblocco'  and idGestioneLead ='$idSponsorizzata'";
//echo $queryCrmVodafone;
        $risultatoTim = $conn19->query($queryCrmTim);
        while ($rigaTim = $risultatoTim->fetch_array()) {
            $timTot = (($rigaTim[0] == null) ? 0 : $rigaTim[0]);
            $timOk = (($rigaTim[1] == null) ? 0 : $rigaTim[1]);
            $timKo = (($rigaTim[2] == null) ? 0 : $rigaTim[2]);
            $valoreMedioTim = $timOk * $mediaTim;
            $dataContrattoTim = (($rigaTim[5] == null) ? '0000-00-00' : $rigaTim[5]);
            $timPostOk = (($rigaTim[6] == null) ? 0 : $rigaTim[6]);
        }




        $somma = $pleniTot + $vodaTot + $viviTot + $irenTot + $unionTot + $timTot;
        $postOk = $pleniPostOk + $vodaPostOk + $viviPostOk + $irenPostOk + $unionPostOk + $timPostOk;
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
         * Ricerca operatore
         */
//        $querySede="SELECT sede FROM `stringheTotale`  where nomeCompleto=''$operatore";
//        $risultatoSede=$conn19->query($querySede);
//        if(($risultatoSede->num_rows)>0){
//            $rs=$risultatoSede->fetch_array();
//            $sede=$rs[0];
//        }else{
//            $sede="-";
//        }
        $sede = "-";

        /**
         * Inseriemnto
         */
        $queryInserimento = "INSERT INTO `gestioneLead`"
                . "( `idSponsorizzata`, `nome`, `cognome`, `mail`, `utmCampagna`, `utmMedium`, `utmSource`, `ip`, `dataImport`, `origine`, `brand`, `leadId`, `source`,agenzia,"
                . "pleniTot,pleniOk,pleniKo,"
                . "vodaTot,vodaOk,vodaKo,"
                . "viviTot,ViviOk,viviKo,"
                . "irenTot,irenOk,irenKo,"
                . "valoreMediaPleni,valoreMediaVivi,valoreMedioVoda,valoreMedioIren,"
                . "categoriaPrima,CategoriaUltima,gestitoDa,dataContrattoPleni,"
                . "dataContrattoIren,dataContrattoVivi,dataContrattoVoda,"
                . "uniTot,uniOk,uniKo,valoreMedioUni,"
                . "convertito,categoriaCampagna,sedeOperatore,"
                . "enelTot,enelOk,enelKo,valoreMedioEnel,"
                . "timTot,timOk,timKo,valoreMedioTim,dataContrattoTim,"
                . " postOk) "
                . "VALUES "
                . "('$idSponsorizzata','$nome','$cognome','$mail','$utmC','$utmM','$utmS','$ip','$important','$origine','$brand','$leadId','$source','$agenzia',"
                . "'$pleniTot','$pleniOk','$pleniKo',"
                . "'$vodaTot','$vodaOk','$vodaKo',"
                . "'$viviTot','$viviOk','$viviKo',"
                . "'$irenTot','$irenOk','$irenKo',"
                . "'$valoreMedioPleni','$valoreMediaVivi','$valoreMedioVoda','$valoreMedioIren',"
                . "'$categoriaEsitoPrima','$categoriaEsitoUltima','$operatoreUltima',"
                . "'$dataContrattoPleni','$dataContrattoIren','$dataContrattoVivi','$dataContrattoVoda',"
                . "'$unionTot','$unionOk','$unionKo','$valoreMedioUnion',"
                . "'$convertito','$categoria','$sede',"
                . "'$EnelTot','$EnelOk','$EnelKo','$valoreMedioEnel',"
                . "'$timTot','$timOk','$timKo','$valoreMedioTim','$dataContrattoTim',"
                . "'$postOk')";
//echo $queryInserimento;
//echo "<br>";


        try {
            $conn19->query($queryInserimento);
        } catch (Exception $ex) {
            
        }
    }
}

/*
 * Gestione chiamata
 */


$queryRicerca = "SELECT "
        . "gl.gestionechiamatano as idSponsorizzate, "
        . "glCF.cf_4437 as nome, "
        . "glCF.cf_4439 as cognome, "
        . "glCF.cf_4441 as mail, "
        . "glCF.cf_4443 as UTMc, "
        . "glCF.cf_4445 as UTMm, "
        . "glCF.cf_4447 as UTMs, "
        . "glCF.cf_4449 as ip, "
        . "glCF.cf_4451 as 'data import', "
        . "glCF.cf_4453 as origine, "
        . "glCF.cf_4455 as brand, "
        . "glCF.cf_4459 as leadId, "
        . "glCF.cf_4475 as lista, "
        . "glCF.cf_4461 as categoraiaEsitoPrima, "
        . "glCF.cf_4465 as agenzia, "
        . " glCF.cf_4469 as operatore, "
        . " glCF.cf_4687 as duplicato "
        . "FROM "
        . "vtiger_gestionechiamata as gl "
        . "inner join vtiger_gestionechiamatacf as glCF on glCF.gestionechiamataid=gl.gestionechiamataid "
        . "inner join vtiger_crmentity as e on glCF.gestionechiamataid=e.crmid "
        . "where "
        . " e.deleted=0 and glCF.cf_4451>'2025-08"
        . "-01'"
        . " ";

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
    $queryTruncate = "TRUNCATE TABLE `gestioneChiamata`";
    $conn19->query($queryTruncate);

    $dataTruncate = date('Y-m-d H:i:s');
    $queryTruncateLog = "INSERT INTO `logImport`(`datImport`, `provenienza`, `descrizione`,stato,idStato) VALUES ('$dataTruncate','$provenienza','Truncate $provenienza',0,'$idStato')";
    $conn19->query($queryTruncateLog);

    while ($riga = $risultato->fetch_array()) {
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
        $lista = $riga[12];
        $categoriaEsitoPrima = $riga[13];
        $agenzia = $riga[14];
        $operatore = $riga[15];
        $duplicato = $riga[16];

        switch ($utmC) {
            case "Search_Call_Generale_10_06_2024":
            case "Search_Call_Nuovi_Allacci_Volture":
            case "Search_Call_Regioni_Nord_Italia":
            case "Search_Call_Brand":
            case "Search_Call_Brand_Comparatore":
            case "Search_Call_Telco_Fibra_internet":
            case "Meta_Nativ_Gen_Luce_Gas":
            case "FormGenerico":
            case "Search_Call_Parola_Ottimizzata":
            case "Search_Call_Telco_Guasti_NS":
            case "Search_Call_Telco_Fibra_internet_NS":


                $origine = "NovaDirect";
                $agenzia = "NovaDirect";
                break;

            case "Search_Call_NS_Nord_Italia_2":
            case "Search_Call_Brand_NS_30_09":
            case "Search_Call_Servizio_Clienti_10_07_24":
            case "Search_Call_NS_Allacci_Volture":


                $origine = "NovaStart";
                $agenzia = "NovaStart";
                break;
            case "DGT_Search_Call_Brand_2025":
            case "Search call Digital Comparatore":
            case "DGT_Search_Call_Nord_Italia_2025":
            case "DGT_Search_Call_Generale_2025":
            case "DGT_Search_Call_Allacci_Volture_2025":
                $origine = "DgtMedia";
                $agenzia = "DgtMedia";
                break;
        }


        switch ($agenzia) {
            case "Muza":
                $agenzia = "GTEnergie";
                break;
            default :
                break;
        }

        $meseRicerca = date('Y-m-01', strtotime($important));

        $queryCrm = "SELECT "
                . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)),data,sum(if(fasePost='OK',pezzoLordo,0)) "
                . "FROM "
                . "plenitude "
                . "inner JOIN aggiuntaPlenitude on plenitude.id=aggiuntaPlenitude.id "
                . "WHERE "
                . " statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco'  and idGestioneLead='$idSponsorizzata'";
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
            $dataContrattoPleni = (($rigaPleni[5] == null) ? '0000-00-00' : $rigaPleni[5]);
            $pleniPostOk = (($rigaPleni[6] == null) ? 0 : $rigaPleni[6]);
        }

        /*
         * Enel
         */
        $queryCrm = "SELECT "
                . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)),data,sum(if(fasePost='OK',pezzoLordo,0)) "
                . "FROM "
                . "enel "
                . "inner JOIN aggiuntaEnel on enel.id=aggiuntaEnel.id "
                . "WHERE "
                . " statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco'  and idGestioneLead='$idSponsorizzata'";
//echo $queryCrm;
//echo "<br>";
        $risultatoEnel = $conn19->query($queryCrm);
        $queryMediaP = "SELECT media FROM `mediaPraticaMese` where mandato='Enel' and mese='$meseRicerca'";
//echo $queryMedia;
        $risultatoMediaP = $conn19->query($queryMediaP);
        $conteggioP = $risultatoMediaP->num_rows;
        if ($conteggioP > 0) {
            $rigaMediaP = $risultatoMediaP->fetch_array();
            $mediaEnel = $rigaMediaP[0];
        } else {
            $mediaEnel = 0;
        }
        while ($rigaEnel = $risultatoEnel->fetch_array()) {
            $enelTot = (($rigaEnel[0] == null) ? 0 : $rigaEnel[0]);
            $enelOk = (($rigaEnel[1] == null) ? 0 : $rigaEnel[1]);
            $enelKo = (($rigaEnel[2] == null) ? 0 : $rigaEnel[2]);
            $valoreMedioEnel = $enelOk * $mediaEnel;
            $dataContrattoEnel = (($rigaEnel[5] == null) ? '0000-00-00' : $rigaEnel[5]);
            $enelPostOk = (($rigaEnel[6] == null) ? 0 : $rigaEnel[6]);
        }


        /**
         * Iren
         */
        $queryCrm = "SELECT "
                . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)),data,sum(if(fasePost='OK',pezzoLordo,0)) "
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
            $dataContrattoIren = (($rigaIren[5] == null || $rigaIren[5] == 0) ? '0000-00-00' : $rigaIren[5]);
            $irenPostOk = (($rigaIren[6] == null) ? 0 : $rigaIren[6]);
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
                . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)),data,sum(if(fasePost='OK',pezzoLordo,0)) "
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
            $dataContrattoVivi = (($rigaVivigas[5] == null) ? '0000-00-00' : $rigaVivigas[5]);
            $viviPostOk = (($rigaVivigas[6] == null) ? 0 : $rigaVivigas[6]);
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
            $dataContrattoVoda = (($rigaVodafone[5] == null) ? '0000-00-00' : $rigaVodafone[5]);

            $valoreMedioVoda = $vodaOk * $mediaVoda;
            $vodaPostOk = 0;
        }


        /**
         * Union
         */
        $queryCrm = "SELECT "
                . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)),data,sum(if(fasePost='OK',pezzoLordo,0)) "
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
            $unionPostOk = (($rigaUnion[6] == null) ? 0 : $rigaUnion[6]);
        }

        /**
         * Tim
         */
        $queryMediaTim = "SELECT media FROM `mediaPraticaMese` where mandato='Tim' and mese='$meseRicerca'";
        $risultatoMediaTim = $conn19->query($queryMediaTim);
        $conteggioTim = $risultatoMediaTim->num_rows;
        if ($conteggioTim > 0) {
            $rigaMediaTim = $risultatoMediaTim->fetch_array();
            $mediaTim = $rigaMediaTim[0];
        } else {
            $mediaTim = 0;
        }


        $queryCrmTim = "SELECT "
                . "sum(pezzoLordo),sum(if(fasePDA ='OK',pezzoLordo,0)),sum(if(fasePDA ='KO',pezzoLordo,0)), sum(if(fasePDA ='BKL',pezzoLordo,0)), sum(if(fasePDA ='BKLP',pezzoLordo,0)),dataVendita,sum(if(fasePost='OK',pezzoLordo,0)) "
                . "FROM "
                . "tim "
                . "inner JOIN aggiuntaTim on tim.id=aggiuntaTim.id "
                . "WHERE "
                . " statoPDA<>'bozza' and statoPDA<>'annullata' and statoPDA<>'pratica doppia' and statoPDA<>'In attesa Sblocco'  and idGestioneLead ='$idSponsorizzata'";
//echo $queryCrmVodafone;
        $risultatoTim = $conn19->query($queryCrmTim);
        while ($rigaTim = $risultatoTim->fetch_array()) {
            $timTot = (($rigaTim[0] == null) ? 0 : $rigaTim[0]);
            $timOk = (($rigaTim[1] == null) ? 0 : $rigaTim[1]);
            $timKo = (($rigaTim[2] == null) ? 0 : $rigaTim[2]);
            $valoreMedioTim = $timOk * $mediaTim;
            $dataContrattoTim = (($rigaTim[5] == null) ? '0000-00-00' : $rigaTim[5]);
            $timPostOk = (($rigaTim[6] == null) ? 0 : $rigaTim[6]);
        }

        $somma = $pleniTot + $vodaTot + $viviTot + $irenTot + $unionTot + $timTot;
        $postOk = $pleniPostOk + $vodaPostOk + $viviPostOk + $irenPostOk + $unionPostOk + $timPostOk;
        if ($somma > 0) {
            $convertito = 1;
        } else {
            $convertito = 0;
        }

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
         * Ricerca operatore
         */
        $querySede = "SELECT sede FROM `stringheTotale`  where nomeCompleto='$operatore'";
        $risultatoSede = $conn19->query($querySede);
        if (($risultatoSede->num_rows) > 0) {
            $rs = $risultatoSede->fetch_array();
            $sede = $rs[0];
        } else {
            $sede = "-";
        }


        /**
         * Inseriemnto
         */
        $queryInserimento = "INSERT INTO"
                . " `gestioneLead` "
                . " (`idSponsorizzata`, `nome`, `cognome`, `mail`, `utmCampagna`, `utmMedium`, `utmSource`, `ip`, `dataImport`, `origine`, `brand`, `leadId`, `source`,agenzia,"
                . "pleniTot,pleniOk,pleniKo,"
                . "vodaTot,vodaOk,vodaKo,"
                . "viviTot,ViviOk,viviKo,"
                . "irenTot,irenOk,irenKo,"
                . "valoreMediaPleni,valoreMediaVivi,valoreMedioVoda,valoreMedioIren,"
                . "categoriaPrima,CategoriaUltima,gestitoDa,convertito,duplicato,"
                . "uniTot,uniOk,uniKo,"
                . "valoreMedioUni,categoriaCampagna,sedeOperatore,"
                . "enelTot,enelOk,enelKo,valoreMedioEnel,dataContrattoEnel,"
                . " timTot,timOk,timKo,valoreMedioTim,dataContrattoTim,"
                . "postOk) "
                . " VALUES "
                . "('$idSponsorizzata','$nome','$cognome','$mail','$utmC','$utmM','$utmS','$ip','$important','$origine','$brand','$leadId','$lista','$agenzia',"
                . "'$pleniTot','$pleniOk','$pleniKo',"
                . "'$vodaTot','$vodaOk','$vodaKo',"
                . "'$viviTot','$viviOk','$viviKo',"
                . "'$irenTot','$irenOk','$irenKo',"
                . "'$valoreMedioPleni','$valoreMediaVivi','$valoreMedioVoda','$valoreMedioIren',"
                . "'$categoriaEsitoPrima','-','$operatore','$convertito','$duplicato',"
                . "'$unionTot','$unionOk','$unionKo',"
                . "'$valoreMedioUnion','$categoria','$sede',"
                . "'$enelTot','$enelOk','$enelKo','$valoreMedioEnel','$dataContrattoEnel',"
                . "'$timTot','$timOk','$timKo','$valoreMedioTim','$dataContrattoTim',"
                . "'$postOk')";
//echo $queryInserimento;
//echo "<br>";
        try {
            $conn19->query($queryInserimento);
        } catch (Exception $e) {
            echo "Per il Valore:" . $idSponsorizzata . " = " . $e->getMessage() . "<br>";
        }
    }
}









//
///*
// * Gestione Leasys
// */
//
//
//$queryRicerca = "SELECT "
//        . "gl.leasysno as idSponsorizzate, "
//        . "glCF.cf_2784 as nome, "
//        . "glCF.cf_2786 as cognome, "
//        . "glCF.cf_2792 as mail, "
//        . "glCF.cf_4741 as UTMc, "
//        . "glCF.cf_4743 as UTMm, "
//        . "glCF.cf_4739 as UTMs, "
//        . "glCF.cf_2984 as ip, "
//        . "glCF.cf_2982 as 'data import', "
//        . " 'Leasys', "
//        . "glCF.cf_2802 as brand, "
//        . "glCF.cf_2798 as leadId, "
//        . "'1038', "
//        . "'', "
//        . "'NovaMarketing', "
//        . "'' , "
//        . " 'No' "
//        . "FROM "
//        . "vtiger_leasys as gl "
//        . "inner join vtiger_leasyscf as glCF on glCF.leasysid=gl.leasysid "
//        . "inner join vtiger_crmentity as e on glCF.leasysid=e.crmid "
//        . "where "
//        . " e.deleted=0 "
//        . "AND e.createdtime >'2024-03-01'";
//
//echo $queryRicerca;
//$risultato = $connCrm->query($queryRicerca);
///**
// * Se la ricerca da errore segna nei log l'errore
// */
//if (!$risultato) {
//    $dataErrore = date('Y-m-d H:i:s');
//    $errore = $connCrm->real_escape_string($connCrm->error);
//    $queryErroreLog = "INSERT INTO `logImport`(`datImport`, `provenienza`, `descrizione`,stato,idStato) VALUES ('$dataErrore','$provenienza','$errore',0,'$idStato')";
//    $conn19->query($queryErroreLog);
//}
///**
// * se non da errore svuota la tabella, segnando nei log l'operazione, e la ricarica riga per riga da zero
// */ else {
////    $queryTruncate = "TRUNCATE TABLE `gestioneChiamata`";
////    $conn19->query($queryTruncate);
//
//    $dataTruncate = date('Y-m-d H:i:s');
//    $queryTruncateLog = "INSERT INTO `logImport`(`datImport`, `provenienza`, `descrizione`,stato,idStato) VALUES ('$dataTruncate','$provenienza','Truncate $provenienza',0,'$idStato')";
//    $conn19->query($queryTruncateLog);
//
//    while ($riga = $risultato->fetch_array()) {
//        $idSponsorizzata = $riga[0];
//        $nome = $conn19->real_escape_string($riga[1]);
//        $cognome = $conn19->real_escape_string($riga[2]);
//        $mail = $riga[3];
//        $utmC = $riga[4];
//        $utmM = $riga[5];
//        $utmS = $riga[6];
//        $ip = $riga[7];
//        $important = $riga[8];
//        $origine = $riga[9];
//        $brand = $riga[10];
//        $leadId = $riga[11];
//        $lista = $riga[12];
//        $categoriaEsitoPrima = $riga[13];
//        $agenzia = $riga[14];
//        $operatore = $riga[15];
//        $duplicato = $riga[16];
//
//        $meseRicerca = date('Y-m-01', strtotime($important));
//
//        $queryCrm = "SELECT "
//                . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)),data "
//                . "FROM "
//                . "plenitude "
//                . "inner JOIN aggiuntaPlenitude on plenitude.id=aggiuntaPlenitude.id "
//                . "WHERE "
//                . " statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco'  and idGestioneLead='$idSponsorizzata'";
////echo $queryCrm;
////echo "<br>";
//        $risultatoPleni = $conn19->query($queryCrm);
//        $queryMediaP = "SELECT media FROM `mediaPraticaMese` where mandato='Plenitude' and mese='$meseRicerca'";
////echo $queryMedia;
//        $risultatoMediaP = $conn19->query($queryMediaP);
//        $conteggioP = $risultatoMediaP->num_rows;
//        if ($conteggioP > 0) {
//            $rigaMediaP = $risultatoMediaP->fetch_array();
//            $mediaPleni = $rigaMediaP[0];
//        } else {
//            $mediaPleni = 0;
//        }
//        while ($rigaPleni = $risultatoPleni->fetch_array()) {
//            $pleniTot = (($rigaPleni[0] == null) ? 0 : $rigaPleni[0]);
//            $pleniOk = (($rigaPleni[1] == null) ? 0 : $rigaPleni[1]);
//            $pleniKo = (($rigaPleni[2] == null) ? 0 : $rigaPleni[2]);
//            $valoreMedioPleni = $pleniOk * $mediaPleni;
//            $dataContrattoPleni = (($rigaPleni[3] == null) ? '0000-00-00' : $rigaPleni[3]);
//        }
//        /**
//         * Iren
//         */
//        $queryCrm = "SELECT "
//                . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)),data "
//                . "FROM "
//                . "iren "
//                . "inner JOIN aggiuntaIren on iren.id=aggiuntaIren.id "
//                . "WHERE "
//                . " statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco'  and idGestioneLead='$idSponsorizzata'";
////echo $queryCrm;
////echo "<br>";
//        $risultatoIren = $conn19->query($queryCrm);
//        $queryMediaP = "SELECT media FROM `mediaPraticaMese` where mandato='Iren' and mese='$meseRicerca'";
////echo $queryMedia;
//        $risultatoMediaP = $conn19->query($queryMediaP);
//        $conteggioP = $risultatoMediaP->num_rows;
//        if ($conteggioP > 0) {
//            $rigaMediaP = $risultatoMediaP->fetch_array();
//            $mediaIren = $rigaMediaP[0];
//        } else {
//            $mediaIren = 0;
//        }
//        while ($rigaIren = $risultatoIren->fetch_array()) {
//            $irenTot = (($rigaIren[0] == null) ? 0 : $rigaIren[0]);
//            $irenOk = (($rigaIren[1] == null) ? 0 : $rigaIren[1]);
//            $irenKo = (($rigaIren[2] == null) ? 0 : $rigaIren[2]);
//            $valoreMedioIren = $irenOk * $mediaIren;
//            $dataContrattoIren = (($rigaIren[3] == null || $rigaIren[3] == 0) ? '0000-00-00' : $rigaIren[3]);
//        }
//
//        /**
//         * vivigas
//         */
//        $queryMediaV = "SELECT media FROM `mediaPraticaMese` where mandato='Vivigas' and mese='$meseRicerca'";
//        $risultatoMediaV = $conn19->query($queryMediaV);
//        $conteggioV = $risultatoMediaV->num_rows;
//        if ($conteggioV > 0) {
//            $rigaMediaV = $risultatoMediaV->fetch_array();
//            $mediaVivi = $rigaMediaV[0];
//        } else {
//            $mediaVivi = 0;
//        }
//        $queryCrmVivigas = "SELECT "
//                . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)),data "
//                . "FROM "
//                . "vivigas "
//                . "inner JOIN aggiuntaVivigas on vivigas.id=aggiuntaVivigas.id "
//                . "WHERE "
//                . " statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco'  and idGestioneLead='$idSponsorizzata'";
////echo $queryCrmVodafone;
//        $risultatoVivigas = $conn19->query($queryCrmVivigas);
//        while ($rigaVivigas = $risultatoVivigas->fetch_array()) {
//            $viviTot = (($rigaVivigas[0] == null) ? 0 : $rigaVivigas[0]);
//            $viviOk = (($rigaVivigas[1] == null) ? 0 : $rigaVivigas[1]);
//            $viviKo = (($rigaVivigas[2] == null) ? 0 : $rigaVivigas[2]);
//            $valoreMediaVivi = $viviOk * $mediaVivi;
//            $dataContrattoVivi = (($rigaVivigas[3] == null) ? '0000-00-00' : $rigaVivigas[3]);
//        }
//        /**
//         * vodafone
//         */
//        $queryMediaVo = "SELECT media FROM `mediaPraticaMese` where mandato='Vodafone' and mese='$meseRicerca'";
//        $risultatoMediaVo = $conn19->query($queryMediaVo);
//        $conteggioVo = $risultatoMediaVo->num_rows;
//        if ($conteggioVo > 0) {
//            $rigaMediaVo = $risultatoMediaVo->fetch_array();
//            $mediaVoda = $rigaMediaVo[0];
//        } else {
//            $mediaVoda = 0;
//        }
//
//
//        $queryCrmVodafone = "SELECT "
//                . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)),dataVendita "
//                . "FROM "
//                . "vodafone "
//                . "inner JOIN aggiuntaVodafone on vodafone.id=aggiuntaVodafone.id "
//                . "WHERE "
//                . " statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco'  and idGestioneLead='$idSponsorizzata'";
////echo $queryCrmVodafone;
//        $risultatoVodafone = $conn19->query($queryCrmVodafone);
//        while ($rigaVodafone = $risultatoVodafone->fetch_array()) {
//            $vodaTot = (($rigaVodafone[0] == null) ? 0 : $rigaVodafone[0]);
//            $vodaOk = (($rigaVodafone[1] == null) ? 0 : $rigaVodafone[1]);
//            $vodaKo = (($rigaVodafone[2] == null) ? 0 : $rigaVodafone[2]);
//            $dataContrattoVoda = (($rigaVodafone[3] == null) ? '0000-00-00' : $rigaVodafone[3]);
//
//            $valoreMedioVoda = $vodaOk * $mediaVoda;
//        }
//
//
//        /**
//         * Union
//         */
//        $queryCrm = "SELECT "
//                . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)),data "
//                . "FROM "
//                . "know.union "
//                . "inner JOIN aggiuntaUnion on know.union.id=aggiuntaUnion.id "
//                . "WHERE "
//                . " statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco'  and idGestioneLead='$idSponsorizzata'";
////echo $queryCrm;
////echo "<br>";
//        $risultatoUnion = $conn19->query($queryCrm);
//        $queryMediaP = "SELECT media FROM `mediaPraticaMese` where mandato='union' and mese='$meseRicerca'";
////echo $queryMedia;
//        $risultatoMediaP = $conn19->query($queryMediaP);
//        $conteggioP = $risultatoMediaP->num_rows;
//        if ($conteggioP > 0) {
//            $rigaMediaP = $risultatoMediaP->fetch_array();
//            $mediaUnion = $rigaMediaP[0];
//        } else {
//            $mediaUnion = 0;
//        }
//        while ($rigaUnion = $risultatoUnion->fetch_array()) {
//            $unionTot = (($rigaUnion[0] == null) ? 0 : $rigaUnion[0]);
//            $unionOk = (($rigaUnion[1] == null) ? 0 : $rigaUnion[1]);
//            $unionKo = (($rigaUnion[2] == null) ? 0 : $rigaUnion[2]);
//            $valoreMedioUnion = $unionOk * $mediaUnion;
//        }
//
//
//
//        $somma = $pleniTot + $vodaTot + $viviTot + $irenTot + $unionTot;
//        if ($somma > 0) {
//            $convertito = 1;
//        } else {
//            $convertito = 0;
//        }
//
//
//        $queryCategoria = "SELECT * FROM `categoriaCampagna` where utmCampagna ='$utmC'";
//        $risultatoCategoria = $conn19->query($queryCategoria);
//        $conteggioCategoria = $risultatoCategoria->num_rows;
//        if ($conteggioCategoria == 0) {
//            $queryInserimentoCategoria = "INSERT INTO `categoriaCampagna`( `utmCampagna`) VALUES ('$utmC')";
//            $conn19->query($queryInserimentoCategoria);
//            $idCategoria = $conn19->insert_id;
//        } else {
//            $rigaCategoria = $risultatoCategoria->fetch_array();
//            $categoria = $rigaCategoria[2];
//        }
//
//        /**
//         * Ricerca operatore
//         */
//        $querySede = "SELECT sede FROM `stringheTotale`  where nomeCompleto='$operatore'";
//        $risultatoSede = $conn19->query($querySede);
//        if (($risultatoSede->num_rows) > 0) {
//            $rs = $risultatoSede->fetch_array();
//            $sede = $rs[0];
//        } else {
//            $sede = "-";
//        }
//
//
//
//        /**
//         * Inseriemnto
//         */
//        $queryInserimento = "INSERT INTO"
//                . " `gestioneLead` "
//                . " (`idSponsorizzata`, `nome`, `cognome`, `mail`, `utmCampagna`, `utmMedium`, `utmSource`, `ip`, `dataImport`, `origine`, `brand`, `leadId`, `source`,agenzia,pleniTot,pleniOk,pleniKo,vodaTot,vodaOk,vodaKo,viviTot,ViviOk,viviKo,irenTot,irenOk,irenKo,valoreMediaPleni,valoreMediaVivi,valoreMedioVoda,valoreMedioIren,categoriaPrima,CategoriaUltima,gestitoDa,convertito,duplicato,uniTot,uniOk,uniKo,valoreMedioUni,categoriaCampagna,sedeOperatore) "
//                . " VALUES "
//                . "('$idSponsorizzata','$nome','$cognome','$mail','$utmC','$utmM','$utmS','$ip','$important','$origine','$brand','$leadId','$lista','$agenzia','$pleniTot','$pleniOk','$pleniKo','$vodaTot','$vodaOk','$vodaKo','$viviTot','$viviOk','$viviKo','$irenTot','$irenOk','$irenKo','$valoreMedioPleni','$valoreMediaVivi','$valoreMedioVoda','$valoreMedioIren','$categoriaEsitoPrima','-','$operatore','$convertito','$duplicato','$unionTot','$unionOk','$unionKo','$valoreMedioUnion','$categoria','$sede')";
////echo $queryInserimento;
////echo "<br>";
//        $conn19->query($queryInserimento);
//    }
//}



$obj19->chiudiConnessione();
$objCrm->chiudiConnessioneCrm();
?>
