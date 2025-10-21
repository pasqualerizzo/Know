<?php

function truncateStringheTotali($_conn) {
    $queryTruncate = "TRUNCATE TABLE `stringheTotale`";
    try {
        $_conn->query($queryTruncate);
    } catch (Exception $e) {
        echo "Si è presentata la seguente eccezzione: $e->getMessage()\n";
    }
    echo "fatto" . "<br>";
}

function caricamentoStringheTotaleSiscall2($_conn) {
    $queryRicerca = "Select * from stringheSiscall2";
    try {
        $risultato = $_conn->query($queryRicerca);
    } catch (Exception $e) {
        echo "Si è presentata la seguente eccezzione: $e->getMessage()\n";
    }
    while ($riga = $risultato->fetch_array()) {
        $id = $riga[0];
        $user = $riga[1];
        $nomeCompleto = $riga[2];
        $livello = $riga[3];
        $idSede = $riga[4];
        $sede = $riga[5];
        $giorno = $riga[6];
        $mese = $riga[7];
        $userGroup = $riga[8];
        $idMandato = $riga[9];
        $mandato = $riga[10];
        $pause = $riga[11];
        $wait = $riga[12];
        $talk = $riga[13];
        $dispo = $riga[14];
        $numero = $riga[15];
        $dead = $riga[16];
        $dataAssunzione = $riga[17];
        $nomeCognome = $riga[18];
        $dataImport = $riga[19];
        $provenienza = $riga[20];

        $queryInserimento = "INSERT INTO `stringheTotale`"
                . "(`user`, `nomeCompleto`, `livello`, `sede`, `giorno`, `mese`, `userGroup`, `mandato`, `pause`, `wait`, `talk`, `dispo`, `numero`, `dead`, `dataAssunzione`, `NomeCognome`, `dataImport`, `provenienza`,idSede,idMandato) "
                . "VALUES ('$user','$nomeCompleto','$livello','$sede','$giorno','$mese','$userGroup','$mandato','$pause','$wait','$talk','$dispo','$numero','$dead','$dataAssunzione','$nomeCognome','$dataImport','$provenienza','$idSede','$idMandato')";
        $_conn->query($queryInserimento);
    }
    echo "fatto" . "<br>";
}

function caricamentoStringheTotaleSiscallLead($_conn) {
    $queryRicerca = "Select * from stringheSiscallLead";
    try {
        $risultato = $_conn->query($queryRicerca);
    } catch (Exception $e) {
        echo "Si è presentata la seguente eccezzione: $e->getMessage()\n";
    }
    while ($riga = $risultato->fetch_array()) {
        $id = $riga[0];
        $user = $riga[1];
        $nomeCompleto = $riga[2];
        $livello = $riga[3];
        $idSede = $riga[4];
        $sede = $riga[5];
        $giorno = $riga[6];
        $mese = $riga[7];
        $userGroup = $riga[8];
        $idMandato = $riga[9];
        $mandato = $riga[10];
        $pause = $riga[11];
        $wait = $riga[12];
        $talk = $riga[13];
        $dispo = $riga[14];
        $numero = $riga[15];
        $dead = $riga[16];
        $dataAssunzione = $riga[17];
        $nomeCognome = $riga[18];
        $dataImport = $riga[19];
        $provenienza = $riga[20];

        $queryInserimento = "INSERT INTO `stringheTotale`"
                . "(`user`, `nomeCompleto`, `livello`, `sede`, `giorno`, `mese`, `userGroup`, `mandato`, `pause`, `wait`, `talk`, `dispo`, `numero`, `dead`, `dataAssunzione`, `NomeCognome`, `dataImport`, `provenienza`,idSede,idMandato) "
                . "VALUES ('$user','$nomeCompleto','$livello','$sede','$giorno','$mese','$userGroup','$mandato','$pause','$wait','$talk','$dispo','$numero','$dead','$dataAssunzione','$nomeCognome','$dataImport','$provenienza','$idSede','$idMandato')";
        $_conn->query($queryInserimento);
    }
    echo "fatto" . "<br>";
}

function caricamentoStringheTotaleSiscall4($_conn) {
    $queryRicerca = "Select * from stringheSiscall4";
    try {
        $risultato = $_conn->query($queryRicerca);
    } catch (Exception $e) {
        echo "Si è presentata la seguente eccezzione: $e->getMessage()\n";
    }
    while ($riga = $risultato->fetch_array()) {
        $id = $riga[0];
        $user = $riga[1];
        $nomeCompleto = $riga[2];
        $livello = $riga[3];
        $idSede = $riga[4];
        $sede = $riga[5];
        $giorno = $riga[6];
        $mese = $riga[7];
        $userGroup = $riga[8];
        $idMandato = $riga[9];
        $mandato = $riga[10];
        $pause = $riga[11];
        $wait = $riga[12];
        $talk = $riga[13];
        $dispo = $riga[14];
        $numero = $riga[15];
        $dead = $riga[16];
        $dataAssunzione = $riga[17];
        $nomeCognome = $riga[18];
        $dataImport = $riga[19];
        $provenienza = $riga[20];

        $queryInserimento = "INSERT INTO `stringheTotale`"
                . "(`user`, `nomeCompleto`, `livello`, `sede`, `giorno`, `mese`, `userGroup`, `mandato`, `pause`, `wait`, `talk`, `dispo`, `numero`, `dead`, `dataAssunzione`, `NomeCognome`, `dataImport`, `provenienza`,idSede,idMandato) "
                . "VALUES ('$user','$nomeCompleto','$livello','$sede','$giorno','$mese','$userGroup','$mandato','$pause','$wait','$talk','$dispo','$numero','$dead','$dataAssunzione','$nomeCognome','$dataImport','$provenienza','$idSede','$idMandato')";
        $_conn->query($queryInserimento);
    }
    echo "fatto" . "<br>";
}

function caricamentoStringheTotaleSiscall4TC($_conn) {
    $queryRicerca = "Select * from stringheSiscall4TC";
    try {
        $risultato = $_conn->query($queryRicerca);
    } catch (Exception $e) {
        echo "Si è presentata la seguente eccezzione: $e->getMessage()\n";
    }
    while ($riga = $risultato->fetch_array()) {
        $id = $riga[0];
        $user = $riga[1];
        $nomeCompleto = $riga[2];
        $livello = $riga[3];
        $idSede = $riga[4];
        $sede = $riga[5];
        $giorno = $riga[6];
        $mese = $riga[7];
        $userGroup = $riga[8];
        $idMandato = $riga[9];
        $mandato = $riga[10];
        $pause = $riga[11];
        $wait = $riga[12];
        $talk = $riga[13];
        $dispo = $riga[14];
        $numero = $riga[15];
        $dead = $riga[16];
        $dataAssunzione = $riga[17];
        $nomeCognome = $riga[18];
        $dataImport = $riga[19];
        $provenienza = $riga[20];

        $queryInserimento = "INSERT INTO `stringheTotale`"
                . "(`user`, `nomeCompleto`, `livello`, `sede`, `giorno`, `mese`, `userGroup`, `mandato`, `pause`, `wait`, `talk`, `dispo`, `numero`, `dead`, `dataAssunzione`, `NomeCognome`, `dataImport`, `provenienza`,idSede,idMandato) "
                . "VALUES ('$user','$nomeCompleto','$livello','$sede','$giorno','$mese','$userGroup','$mandato','$pause','$wait','$talk','$dispo','$numero','$dead','$dataAssunzione','$nomeCognome','$dataImport','$provenienza','$idSede','$idMandato')";
        $_conn->query($queryInserimento);
    }
    echo "fatto" . "<br>";
}

function caricamentoStringheTotaleSiscallGT($_conn) {
    $queryRicerca = "Select * from stringheSiscallGT";
    try {
        $risultato = $_conn->query($queryRicerca);
    } catch (Exception $e) {
        echo "Si è presentata la seguente eccezzione: $e->getMessage()\n";
    }
    while ($riga = $risultato->fetch_array()) {
        $id = $riga[0];
        $user = $riga[1];
        $nomeCompleto = $riga[2];
        $livello = $riga[3];
        $idSede = $riga[4];
        $sede = $riga[5];
        $giorno = $riga[6];
        $mese = $riga[7];
        $userGroup = $riga[8];
        $idMandato = $riga[9];
        $mandato = $riga[10];
        $pause = $riga[11];
        $wait = $riga[12];
        $talk = $riga[13];
        $dispo = $riga[14];
        $numero = $riga[15];
        $dead = $riga[16];
        $dataAssunzione = $riga[17];
        $nomeCognome = $riga[18];
        $dataImport = $riga[19];
        $provenienza = $riga[20];

        $queryInserimento = "INSERT INTO `stringheTotale`"
                . "(`user`, `nomeCompleto`, `livello`, `sede`, `giorno`, `mese`, `userGroup`, `mandato`, `pause`, `wait`, `talk`, `dispo`, `numero`, `dead`, `dataAssunzione`, `NomeCognome`, `dataImport`, `provenienza`,idSede,idMandato) "
                . "VALUES ('$user','$nomeCompleto','$livello','$sede','$giorno','$mese','$userGroup','$mandato','$pause','$wait','$talk','$dispo','$numero','$dead','$dataAssunzione','$nomeCognome','$dataImport','$provenienza','$idSede','$idMandato')";
        $_conn->query($queryInserimento);
    }
    echo "fatto" . "<br>";
}

function caricamentoStringheTotaleSiscall($_conn) {
    $queryRicerca = "Select * from stringheSiscall";
    try {
        $risultato = $_conn->query($queryRicerca);
    } catch (Exception $e) {
        echo "Si è presentata la seguente eccezzione: $e->getMessage()\n";
    }
    while ($riga = $risultato->fetch_array()) {
        $id = $riga[0];
        $user = $riga[1];
        $nomeCompleto = $riga[2];
        $livello = $riga[3];
        $idSede = $riga[4];
        $sede = $riga[5];
        $giorno = $riga[6];
        $mese = $riga[7];
        $userGroup = $riga[8];
        $idMandato = $riga[9];
        $mandato = $riga[10];
        $pause = $riga[11];
        $wait = $riga[12];
        $talk = $riga[13];
        $dispo = $riga[14];
        $numero = $riga[15];
        $dead = $riga[16];
        $dataAssunzione = $riga[17];
        $nomeCognome = $riga[18];
        $dataImport = $riga[19];
        $provenienza = $riga[20];

        $queryInserimento = "INSERT INTO `stringheTotale`"
                . "(`user`, `nomeCompleto`, `livello`, `sede`, `giorno`, `mese`, `userGroup`, `mandato`, `pause`, `wait`, `talk`, `dispo`, `numero`, `dead`, `dataAssunzione`, `NomeCognome`, `dataImport`, `provenienza`,idSede,idMandato) "
                . "VALUES ('$user','$nomeCompleto','$livello','$sede','$giorno','$mese','$userGroup','$mandato','$pause','$wait','$talk','$dispo','$numero','$dead','$dataAssunzione','$nomeCognome','$dataImport','$provenienza','$idSede','$idMandato')";
        $_conn->query($queryInserimento);
    }
    echo "fatto" . "<br>";
}

function caricamentoStringheTotaleSiscallDigital($_conn) {
    $queryRicerca = "Select * from stringheSiscallDigital";
    try {
        $risultato = $_conn->query($queryRicerca);
    } catch (Exception $e) {
        echo "Si è presentata la seguente eccezzione: $e->getMessage()\n";
    }
    while ($riga = $risultato->fetch_array()) {
        $id = $riga[0];
        $user = $riga[1];
        $nomeCompleto = $riga[2];
        $livello = $riga[3];
        $idSede = $riga[4];
        $sede = $riga[5];
        $giorno = $riga[6];
        $mese = $riga[7];
        $userGroup = $riga[8];
        $idMandato = $riga[9];
        $mandato = $riga[10];
        $pause = $riga[11];
        $wait = $riga[12];
        $talk = $riga[13];
        $dispo = $riga[14];
        $numero = $riga[15];
        $dead = $riga[16];
        $dataAssunzione = $riga[17];
        $nomeCognome = $riga[18];
        $dataImport = $riga[19];
        $provenienza = $riga[20];

        $queryInserimento = "INSERT INTO `stringheTotale`"
                . "(`user`, `nomeCompleto`, `livello`, `sede`, `giorno`, `mese`, `userGroup`, `mandato`, `pause`, `wait`, `talk`, `dispo`, `numero`, `dead`, `dataAssunzione`, `NomeCognome`, `dataImport`, `provenienza`,idSede,idMandato) "
                . "VALUES ('$user','$nomeCompleto','$livello','$sede','$giorno','$mese','$userGroup','$mandato','$pause','$wait','$talk','$dispo','$numero','$dead','$dataAssunzione','$nomeCognome','$dataImport','$provenienza','$idSede','$idMandato')";
        $_conn->query($queryInserimento);
    }
    echo "fatto" . "<br>";
}

function truncateFormazioneTemporanea($_conn) {
    try {
        $_conn->query("TRUNCATE TABLE fTot");
    } catch (Exception $e) {
        echo "Si è presentata la seguente eccezzione: $e->getMessage()\n";
    }
}

function caricamentoFormazioneTemporanea($_conn, $_dataControllo) {

    $queryF1 = "SELECT "
            . " user,nomeCompleto,giorno,sum(numero),dataAssunzione "
            . " FROM `stringheTotale` "
            . " where dataAssunzione>='2023-07-01' and giorno>=dataAssunzione and dataAssunzione>='$_dataControllo' "
            . " group by user,giorno";

    try {
        $risultatoF1 = $_conn->query($queryF1);
    } catch (Exception $e) {
        echo "Si è presentata la seguente eccezzione: $e->getMessage()\n";
    }
    while ($rigaF1 = $risultatoF1->fetch_array()) {
        $user = $rigaF1[0];
        $nomeCognome = $rigaF1[1];
        $giorno = $rigaF1[2];
        $numero = $rigaF1[3];
        $dataAssunzione = date('Y-m-d', strtotime($rigaF1[4]));

        $queryInserimentoF1 = "INSERT INTO `fTot`"
                . "(`user`, `nomeCompleto`, `giorno`, `numero`, `dataAssunzione`) "
                . "VALUES ('$user','$nomeCognome','$giorno','$numero','$dataAssunzione')";

        $_conn->query($queryInserimentoF1);
    }
}

function truncateFormazioneTotale($_conn) {
    try {
        $_conn->query("TRUNCATE TABLE formazioneTotale");
    } catch (Exception $e) {
        echo "Si è presentata la seguente eccezzione: $e->getMessage()\n";
    }
}

function caricamentoFormazioneTotale($_conn) {
    $queryRicerca = "SELECT user, nomeCompleto,(sum(numero) over (PARTITION BY user order by giorno ))/3600 as ore,dataAssunzione,giorno,numero "
            . "FROM `fTot` where dataAssunzione>='2023-07-01' and giorno>=dataAssunzione "
            . "group by user,giorno";
    try {
        $risultato = $_conn->query($queryRicerca);
    } catch (Exception $e) {
        echo "Si è presentata la seguente eccezzione: $e->getMessage()\n";
    }

    while ($riga = $risultato->fetch_array()) {
        $user = $riga[0];
        $nomeCompleto = $riga[1];
        $ore = $riga[2];
        $dataAssunzione = date('Y-m-d', strtotime($riga[3]));
        $giorno = $riga[4];
        $lavorato = $riga[5] / 3600;

        $queryInserimento = "INSERT INTO `formazioneTotale`"
                . "(`user`, `nomeCompleto`, `ore`,dataAssunzione,giorno,lavorato) "
                . "VALUES ('$user','$nomeCompleto','$ore','$dataAssunzione','$giorno','$lavorato')";
        try {
            $_conn->query($queryInserimento);
        } catch (Exception $e) {
            echo "Si è presentata la seguente eccezzione: $e->getMessage()\n";
        }
    }
}

function truncatePagamentoGiorno($_conn) {
    $queryTruncate = "TRUNCATE TABLE pagamentoGiorno";
    try {
        $_conn->query($queryTruncate);
    } catch (Exception $e) {
        echo "Si è presentata la seguente eccezzione: $e->getMessage()\n";
    }
}

function pesoVodafone($_conn, $_giorno, $_nomeCompleto) {
    $queryPesoVodafone = "SELECT sum(aggiuntaVodafone.pesoTotaleLordo),sum(aggiuntaVodafone.pesoPagato),sum(aggiuntaVodafone.pesoFormazione) "
            . "FROM `vodafone` inner join aggiuntaVodafone on vodafone.id=aggiuntaVodafone.id where creatoDa='$_nomeCompleto' and dataVendita='$_giorno' "
            . "group by creatoDa,dataVendita";

    $risultatoVodafone = $_conn->query($queryPesoVodafone);

    $risposta = [];
    if (($risultatoVodafone->num_rows) > 0) {
        $riga = $risultatoVodafone->fetch_array();
        $risposta = [
            round($riga[0], 2),
            round($riga[1], 2),
            round($riga[2], 2),
        ];
    } else {
        $risposta = [
            0,
            0,
            0,
        ];
    }

    return $risposta;
}

function pesoVivigas($_conn, $_giorno, $_nomeCompleto) {
    $queryPesoVivigas = "SELECT sum(aggiuntaVivigas.totalePesoLordo),sum(aggiuntaVivigas.pesoTotalePagato),sum(aggiuntaVivigas.pesoFormazione) "
            . "FROM `vivigas` inner join aggiuntaVivigas on vivigas.id=aggiuntaVivigas.id where creatoDa='$_nomeCompleto' and data='$_giorno' "
            . "group by creatoDa,data";

    try {
        $risultatoVivigas = $_conn->query($queryPesoVivigas);
    } catch (Exception $e) {
        echo "Si è presentata la seguente eccezzione: $e->getMessage()\n";
    }
    $risposta = [];
    if (($risultatoVivigas->num_rows) > 0) {
        $riga2 = $risultatoVivigas->fetch_array();
        $risposta = [
            round($riga2[0], 2),
            round($riga2[1], 2),
            round($riga2[2], 2),
        ];
    } else {
        $risposta = [
            0,
            0,
            0,
        ];
    }

    return $risposta;
}

function pesoPlenitude($_conn, $_giorno, $_nomeCompleto) {
    $queryPesoPlenitude = ""
            . " SELECT"
            . " sum(aggiuntaPlenitude.totalePesoLordo),"
            . " sum(aggiuntaPlenitude.pesoTotalePagato),"
            . " sum(aggiuntaPlenitude.pesoFormazione) "
            . " FROM `plenitude` inner join aggiuntaPlenitude on plenitude.id=aggiuntaPlenitude.id "
            . " where creatoDa='$_nomeCompleto' and data='$_giorno' "
            . " AND comodity<>'Polizza' "
            . " group by creatoDa,data";

    $risultatoPlenitude = $_conn->query($queryPesoPlenitude);
    $risposta = [];
    if (($risultatoPlenitude->num_rows) > 0) {
        $riga2 = $risultatoPlenitude->fetch_array();
        $risposta = [
            round($riga2[0], 2),
            round($riga2[1], 2),
            round($riga2[2], 2),
        ];
    } else {
        $risposta = [
            0,
            0,
            0,
        ];
    }
    return $risposta;
}

function pesoPlenitudePolizze($_conn, $_giorno, $_nomeCompleto) {
    $queryPesoPlenitude = ""
            . " SELECT"
            . " sum(aggiuntaPlenitude.totalePesoLordo),"
            . " sum(aggiuntaPlenitude.pesoTotalePagato),"
            . " sum(aggiuntaPlenitude.pesoFormazione) "
            . " FROM `plenitude` inner join aggiuntaPlenitude on plenitude.id=aggiuntaPlenitude.id "
            . " where creatoDa='$_nomeCompleto' and data='$_giorno' "
            . " AND comodity<>'Polizza' "
            . " group by creatoDa,data";

    $risultatoPlenitude = $_conn->query($queryPesoPlenitude);
    $risposta = [];
    if (($risultatoPlenitude->num_rows) > 0) {
        $riga2 = $risultatoPlenitude->fetch_array();
        $risposta = [
            round($riga2[0], 2),
            round($riga2[1], 2),
            round($riga2[2], 2),
        ];
    } else {
        $risposta = [
            0,
            0,
            0,
        ];
    }
    return $risposta;
}

function pesoGreennetwork($_conn, $_giorno, $_nomeCompleto) {

    $queryPesoGreen = "SELECT sum(aggiuntaGreen.totalePesoLordo),sum(aggiuntaGreen.pesoTotalePagato),sum(aggiuntaGreen.pesoFormazione) "
            . "FROM `green` inner join aggiuntaGreen on green.id=aggiuntaGreen.id where creatoDa='$_nomeCompleto' and data='$_giorno' "
            . "group by creatoDa,data";

    $risultatoGreen = $_conn->query($queryPesoGreen);
    $risposta = [];
    if (($risultatoGreen->num_rows) > 0) {
        $riga2 = $risultatoGreen->fetch_array();
        $risposta = [
            round($riga2[0], 2),
            round($riga2[1], 2),
            round($riga2[2], 2),
        ];
    } else {
        $risposta = [
            0,
            0,
            0,
        ];
    }
    return $risposta;
}

function pesoEnel($_conn, $_giorno, $_nomeCompleto) {
    $queryPesoEnelOut = "SELECT sum(aggiuntaEnel.totalePesoLordo),sum(aggiuntaEnel.pesoTotalePagato),sum(aggiuntaEnel.pesoFormazione) "
            . "FROM `enel` inner join aggiuntaEnel on enel.id=aggiuntaEnel.id where creatoDa='$_nomeCompleto' and data='$_giorno' "
            . "group by creatoDa,data";
    $risultatoEnelOut = $_conn->query($queryPesoEnelOut);
    $risposta = [];

    if (($risultatoEnelOut->num_rows) > 0) {
        $riga2 = $risultatoEnelOut->fetch_array();
        $risposta = [
            round($riga2[0], 2),
            round($riga2[1], 2),
            round($riga2[2], 2),
        ];
    } else {
        $risposta = [
            0,
            0,
            0,
        ];
    }

    return $risposta;
}

function pesoIren($_conn, $_giorno, $_nomeCompleto) {
    $queryPesoIren = "SELECT sum(aggiuntaIren.totalePesoLordo),sum(aggiuntaIren.pesoTotalePagato),sum(aggiuntaIren.pesoFormazione) "
            . "FROM `iren` inner join aggiuntaIren on iren.id=aggiuntaIren.id where creatoDa='$_nomeCompleto' and data='$_giorno' "
            . "group by creatoDa,data";

    $risultatoIren = $_conn->query($queryPesoIren);

    $risposta = [];
    if (($risultatoIren->num_rows) > 0) {
        $riga2 = $risultatoIren->fetch_array();
        $risposta = [
            round($riga2[0], 2),
            round($riga2[1], 2),
            round($riga2[2], 2),
        ];
    } else {
        $risposta = [
            0,
            0,
            0,
        ];
    }
    return $risposta;
}

function pesoUnion($_conn, $_giorno, $_nomeCompleto) {
    $queryPesoUnion = "SELECT sum(aggiuntaUnion.totalePesoLordo),sum(aggiuntaUnion.pesoTotalePagato),sum(aggiuntaUnion.pesoFormazione) "
            . "FROM `union` inner join aggiuntaUnion on union.id=aggiuntaUnion.id where creatoDa='$_nomeCompleto' and data='$_giorno' "
            . "group by creatoDa,data";
    $risultatoUnion = $_conn->query($queryPesoUnion);
    $risposta = [];
    if (($risultatoUnion->num_rows) > 0) {
        $riga2 = $risultatoUnion->fetch_array();
        $risposta = [
            round($riga2[0], 2),
            round($riga2[1], 2),
            round($riga2[2], 2),
        ];
    } else {
        $risposta = [
            0,
            0,
            0,
        ];
    }
    return $risposta;
}

function oreFormazione($_conn, $_giorno, $_nomeCompleto) {
    $queryFormazione = "SELECT ore FROM `formazioneTotale` where nomeCompleto='$_nomeCompleto' and giorno='$_giorno'";
    $risultatoFormazione = $_conn->query($queryFormazione);
    $formazione = 0;
    if (($risultatoFormazione->num_rows) > 0) {
        $riga2 = $risultatoFormazione->fetch_array();
        $formazione = $riga2[0];
        if ($formazione > 45) {
            $formazione = 0;
        }
    } else {
        $formazione = 0;
    }
    return $formazione;
}

function orePolizze($_conn, $_giorno, $_nomeCompleto) {
    $queryPolizze = "SELECT numero FROM `stringheSiscallGT` where nomeCompleto='$_nomeCompleto' and giorno='$_giorno' and mandato='Plenitude Polizze'";
//echo $queryFormazione;
    $risultatoPolizze = $_conn->query($queryPolizze);
    if (($risultatoPolizze->num_rows) > 0) {
        $riga2 = $risultatoPolizze->fetch_array();
        $polizzeGt = $riga2[0];
    } else {
        $polizzeGt = 0;
    }

    $queryPolizze2 = "SELECT numero FROM `stringheSiscall2` where nomeCompleto='$_nomeCompleto' and giorno='$_giorno' and mandato='Plenitude Polizze'";

    $risultatoPolizze2 = $_conn->query($queryPolizze2);
    if (($risultatoPolizze2->num_rows) > 0) {
        $riga2 = $risultatoPolizze2->fetch_array();
        $polizze2 = $riga2[0];
    } else {
        $polizze2 = 0;
    }

    $queryPolizze3 = "SELECT numero FROM `stringheSiscallLead` where nomeCompleto='$_nomeCompleto' and giorno='$_giorno' and mandato='Plenitude Polizze'";

    $risultatoPolizze3 = $_conn->query($queryPolizze3);
    if (($risultatoPolizze3->num_rows) > 0) {
        $riga2 = $risultatoPolizze3->fetch_array();
        $polizze3 = $riga2[0];
    } else {
        $polizze3 = 0;
    }

    return $polizze = $polizzeGt + $polizze2 + $polizze3;
}

function caricamentoPagamentoGiorno($_conn) {

    $queryRaggruppamento = ""
            . " SELECT"
            . " nomeCompleto,"
            . " giorno,"
            . " mese,"
            . " livello,"
            . " sum(pause) as pause,"
            . " sum(wait) as wait,"
            . " sum(talk) as talk,"
            . " sum(dispo)as dispo,"
            . " sum(numero) as numero,"
            . " sum(dead) as dead "
            . " FROM stringheTotale "
            . " where giorno>'2023-08-31' "
            . " group by nomeCompleto,giorno";
    $risultatoRaggruppamento = $_conn->query($queryRaggruppamento);
    while ($riga = $risultatoRaggruppamento->fetch_array()) {
        $nomeCompleto = $riga[0];
        $giorno = $riga[1];
        $mese = $riga[2];
        $livello = $riga[3];
        $pause = $riga[4];
        $wait = $riga[5];
        $talk = $riga[6];
        $dispo = $riga[7];
        $numero = $riga[8];
        $dead = $riga[9];

        $vodafone = pesoVodafone($_conn, $giorno, $nomeCompleto);
        $vodafonePesoLordo = $vodafone[0];
        $vodafonePesoPagato = $vodafone[1];
        $vodafonePesoFormazione = $vodafone[2];

        $vivigas = pesoVivigas($_conn, $giorno, $nomeCompleto);
        $vivigasPesoLordo = $vivigas[0];
        $vivigasPesoPagato = $vivigas[1];
        $vivigasPesoFormazione = $vivigas[2];

        $plenitude = pesoPlenitude($_conn, $giorno, $nomeCompleto);
        $plenitudePesoLordo = $plenitude[0];
        $plenitudePesoPagato = $plenitude[1];
        $plenitudePesoFormazione = $plenitude[2];

        $plenitudePolizze = pesoPlenitudePolizze($_conn, $giorno, $nomeCompleto);
        $plenitudePolizzePesoLordo = $plenitudePolizze[0];
        $plenitudePolizzePesoPagato = $plenitudePolizze[1];
        $plenitudePolizzePesoFormazione = $plenitudePolizze[2];

        $greennetwork = pesoGreennetwork($_conn, $giorno, $nomeCompleto);
        $greenPesoLordo = $greennetwork[0];
        $greenPesoPagato = $greennetwork[1];
        $greenPesoFormazione = $greennetwork[2];

        $enelOutPesoLordo = 0;
        $enelOutPesoPagato = 0;
        $enelOutPesoFormazione = 0;

        $enel = pesoEnel($_conn, $giorno, $nomeCompleto);
        $eneltPesoLordo = $enel[0];
        $enelPesoPagato = $enel[1];
        $enelPesoFormazione = $enel[2];

        $iren = pesoIren($_conn, $giorno, $nomeCompleto);
        $irenPesoLordo = $iren[0];
        $irenPesoPagato = $iren[1];
        $irenPesoFormazione = $iren[2];

        $union = pesoUnion($_conn, $giorno, $nomeCompleto);
        $unionPesoLordo = $union[0];
        $unionPesoPagato = $union[1];
        $unionPesoFormazione = $union[2];

        $formazione = oreFormazione($_conn, $giorno, $nomeCompleto);

        $polizze = orePolizze($_conn, $giorno, $nomeCompleto);

        $queryInserimentoRaggruppamento = "INSERT INTO pagamentoGiorno"
                . "(`nomeCompleto`, `livello`, `giorno`, `mese`, `pause`, `wait`, `talk`, `dispo`, `numero`, `dead`,"
                . "vodafonePesoLordo,vodafonePesoPagato,vodafonePesoFormazione,"
                . "vivigasPesoLordo,vivigasPesoPagato,vivigasPesoFormazione, "
                . "plenitudePesoLordo,plenitudePesoPagato,plenitudePesoFormazione, "
                . "greenPesoLordo,greenPesoPagato,greenPesoFormazione,"
                . " enelOutPesoLordo,enelOutPesoPagato,enelOutPesoFormazione,oreFormazione,numeroPolizze,irenPesoLordo,irenPesoPagato,irenPesoFormazione,unionPesoLordo,unionPesoPagato,unionPesoFormazione, "
                . " plenitudePolizzePesoLordo, plenitudePolizzePesoPagato,plenitudePolizzePesoFormazione,"
                . " enelPesoLordo,enelPesoPagato,enelPesoFormazione) "
                . "VALUES ('$nomeCompleto','$livello','$giorno','$mese','$pause','$wait','$talk','$dispo','$numero','$dead',"
                . "'$vodafonePesoLordo','$vodafonePesoPagato','$vodafonePesoFormazione',"
                . "'$vivigasPesoLordo','$vivigasPesoPagato','$vivigasPesoFormazione',"
                . "'$plenitudePesoLordo', '$plenitudePesoPagato','$plenitudePesoFormazione',"
                . "'$greenPesoLordo','$greenPesoPagato','$greenPesoFormazione', "
                . "'$enelOutPesoLordo','$enelOutPesoPagato','$enelOutPesoFormazione','$formazione','$polizze','$irenPesoLordo','$irenPesoPagato','$irenPesoFormazione','$unionPesoLordo','$unionPesoPagato','$unionPesoFormazione',"
                . " '$plenitudePolizzePesoLordo','$plenitudePolizzePesoPagato','$plenitudePolizzePesoFormazione','$eneltPesoLordo','$enelPesoPagato','$enelPesoFormazione' )";

        $_conn->query($queryInserimentoRaggruppamento);
    }
}

function truncatePagamentoMese($_conn) {
    $queryTruncateMese = "TRUNCATE TABLE pagamentoMese";
    $queryTruncateMeseAggiunto = "TRUNCATE TABLE aggiuntaPagamento";
    try {
        $_conn->query($queryTruncateMese);
        $_conn->query($queryTruncateMeseAggiunto);
    } catch (Exception $e) {
        echo "Si è presentata la seguente eccezzione: $e->getMessage()\n";
    }
    echo "fatto" . "<br>";
}

function caricamentoPagamentoMese($_conn, $_connS2) {
    $queryRaggruppamentoMese = "SELECT "
            . " nomeCompleto, "
            . " mese,"
            . " sum(numero),"
            . " sum(vodafonePesoLordo),"
            . " sum(vodafonePesoPagato),"
            . " sum(vodafonePesoFormazione),"
            . " sum(vivigasPesoLordo),"
            . " sum(vivigasPesoPagato),"
            . " sum(vivigasPesoFormazione),"
            . " sum(plenitudePesoLordo),"
            . " sum(plenitudePesoPagato),"
            . " sum(plenitudePesoFormazione),"
            . " sum(greenPesoLordo),"
            . " sum(greenPesoPagato),"
            . " sum(greenPesoFormazione),"
            . " sum(enelOutPesoLordo),"
            . " sum(enelOutPesoPagato),"
            . " sum(enelOutPesoFormazione),"
            . " livello,"
            . " max(oreFormazione),"
            . " sum(numeroPolizze),"
            . " sum(irenPesoLordo),"
            . " sum(irenPesoPagato), "
            . " sum(irenPesoFormazione),"
            . " sum(unionPesoLordo),"
            . " sum(unionPesoPagato),"
            . " sum(unionPesoFormazione), "
            . " sum(plenitudePolizzePesoLordo),"
            . " sum(plenitudePolizzePesoPagato),"
            . " sum(plenitudePolizzePesoFormazione),"
            . " sum(enelPesoLordo),"
            . " sum(enelPesoPagato),"
            . " sum(enelPesoFormazione)"
            . " FROM `pagamentoGiorno` "
            . " group by nomeCompleto,mese";

    $risultatoRaggruppamentoMese = $_conn->query($queryRaggruppamentoMese);

    while ($riga = $risultatoRaggruppamentoMese->fetch_array()) {

        $nomeCompleto = $riga[0];
        $mese = $riga[1];
        $numero = $riga[2] / 3600;
        $vpl = $riga[3];
        $vpp = $riga[4];
        $vpf = $riga[5];
        $vipl = $riga[6];
        $vipp = $riga[7];
        $vipf = $riga[8];
        $ppl = $riga[9];
        $ppp = $riga[10];
        $ppf = $riga[11];
        $gpl = $riga[12];
        $gpp = $riga[13];
        $gpf = $riga[14];
        $epl = $riga[15];
        $epp = $riga[16];
        $epf = $riga[17];
        $livello = $riga[18];
        $formazione = $riga[19];
        $orePolizze = $riga[20] / 3600;
        $ipl = $riga[21];
        $ipp = $riga[22];
        $ipf = $riga[23];
        $upl = $riga[24];
        $upp = $riga[25];
        $upf = $riga[26];
        $plenitudePolizzePesoLordo = $riga[27];
        $plenitudePolizzePesoPagato = $riga[28];
        $plenitudePolizzePesoFormazione = $riga[29];
        $enelPesoLordo = $riga[30];
        $enelPesoPagato = $riga[31];
        $enelPesoFormazione = $riga[32];

        $queryInserimentoMese = "INSERT INTO `pagamentoMese`(`nomeCompleto`, `mese`, `numero`, "
                . "`vodafonePesoLordo`, `vodafonePesoPagato`, `vodafonePesoFormazione`, "
                . "`vivigasPesoLordo`, `vivigasPesoPagato`, `vivigasPesoFormazione`, "
                . "`plenitudePesoLordo`, `plenitudePesoPagato`, `plenitudePesoFormazione`, "
                . "`greenPesoLordo`, `greenPesoPagato`, `greenPesoFormazione`, "
                . "`enelOutPesoLordo`, `enelOutPesoPagato`, `enelOutPesoFormazione`,livello,formazione,orePolizze,irenPesoLordo,irenPesoPagato,irenPesoFormazione,unionPesoLordo,unionPesoPagato,unionPesoFormazione,"
                . " plenitudePolizzePesoLordo,plenitudePolizzePesoPagato,plenitudePolizzePesoFormazione,`enelPesoLordo`, `enelPesoPagato`, `enelPesoFormazione`) "
                . "VALUES "
                . " ('$nomeCompleto','$mese','$numero','$vpl','$vpp','$vpf','$vipl','$vipp','$vipf','$ppl','$ppp','$ppf','$gpl','$gpp','$gpf','$epl','$epp',$epf,"
                . " '$livello','$formazione','$orePolizze','$ipl','$ipp','$ipf','$upl','$upp','$upf', "
                . " '$plenitudePolizzePesoLordo','$plenitudePolizzePesoPagato','$plenitudePolizzePesoFormazione','$enelPesoLordo','$enelPesoPagato','$enelPesoFormazione')";

        $_conn->query($queryInserimentoMese);

        $indice = $_conn->insert_id;
//echo $indice;
        $ore = 0;
        if (($numero + $orePolizze - $formazione) < 0) {
            $ore = 0;
        } else {
            $ore = $numero + $orePolizze - $formazione;
        }
        $puntiPagati = $vpp + $vipp + $ppp + $gpp + $epp + $ipp + $upp + $plenitudePolizzePesoPagato + $enelPesoPagato;
        $puntiFormazione = $vpf + $vipf + $ppf + $gpf + $epf + $ipf + $upf + $plenitudePolizzePesoFormazione + $enelPesoFormazione;
        $punti = 0;
        if ($puntiPagati <= $puntiFormazione) {
            $punti = 0;
        } else {
            $punti = $puntiPagati - $puntiFormazione;
        }
        $meseCompleto = date('Y-m-d', strtotime("01-" . $mese));

        $sede = sedeOperatore($_connS2, $nomeCompleto);
        $valoreGaraPunti = garaPunti($_conn, $punti, $meseCompleto);
        $garaOre = garaOre($_conn, $punti, $ore, $meseCompleto);
        $oreAutorizzate = $garaOre[0];
        $valoreGareOre = $garaOre[1];
        $costoOre = ($ore <= $oreAutorizzate || $oreAutorizzate == 0) ? round($valoreGareOre * $ore, 2) : round($valoreGareOre * $oreAutorizzate, 2);
        $costoPezzi = round($valoreGaraPunti * $punti, 2);
        $costoTotale = $costoOre + $costoPezzi;
        $meseAdattato = date('Y-m-d', strtotime("01-" . $mese));
        $giornoValutazione = ultimoGiornoMese($meseAdattato);
        $oggi = date('Y-m-d');
        if ($oggi > $giornoValutazione) {
            $giorniLavoratiMese = giorniLavoratiMese($giornoValutazione, 0);
        } else {
            $giorniLavoratiMese = giorniLavoratiMese($oggi, 0);
        }

        $costoGiorno = ($giorniLavoratiMese == 0) ? 0 : $costoTotale / $giorniLavoratiMese;
        $costoAzienda = $costoTotale * 1.4;
        $dataRiferimento = date('Y-m-d', strtotime("01-" . $mese));
        $Ore = ($ore <= $oreAutorizzate || $oreAutorizzate == 0) ? $ore : $oreAutorizzate;

        $queryAggiuntaPagamento = "INSERT INTO `aggiuntaPagamento`"
                . " (`id`, `nomeGaraOre`, `oreAutorizzate`, `valoreOre`, `costoOre`, `nomeGaraPezzi`, `valorePezzi`, `costoPezzi`, `costoTotale`, `giorniLavorati`, `costoGiorni`, `costoAzienda`,orePagabili,puntiPagabili,sede,dataRiferimento) "
                . " VALUES "
                . " ('$indice','-','$oreAutorizzate','$valoreGareOre','$costoOre','-','$valoreGaraPunti','$costoPezzi','$costoTotale','$giorniLavoratiMese','$costoGiorno','$costoAzienda','$ore','$punti','$sede','$dataRiferimento')";
        try {
            $_conn->query($queryAggiuntaPagamento);
        } catch (Exception $ex) {
            echo "Si è presentata la seguente eccezzione: $ex->getMessage()\n";
        }
    }
}

function garaPunti($_conn, $_punti, $_mese) {
    $query = "SELECT valore  FROM `garaPunti` where ($_punti between puntiMinimi and puntiMassimi) and mese='$_mese'";
//echo $query;
    $risultato = $_conn->query($query);
    if (($risultato->num_rows) > 0) {
        $riga = $risultato->fetch_array();
        return $riga[0];
    } else {
        return 0;
    }
}

function garaOreMinima($_conn, $_mese) {
    $id = 1;
    $query = "SELECT min(id) FROM `garaOre` where mese='$_mese'";
    //echo $query;
    try {
        $risultato = $_conn->query($query);
    } catch (Exception $ex) {
        echo "Si è presentata la seguente eccezzione: $ex->getMessage()\n";
    }
    $riga = $risultato->fetch_array();
    $id = $riga[0];
    if (is_null($id)) {
        return 1;
    } else {
        return $id;
    }
}

function garaOre($_conn, $_punti, $_ore, $_mese) {
    $idMin = garaOreMinima($_conn, $_mese);
    $query = "SELECT * FROM `garaOre` where ($_punti between pezziMinimi and pezziMassimi) and mese='$_mese'";
    try {
        $risultato = $_conn->query($query);
    } catch (Exception $e) {
        echo "Si è presentata la seguente eccezzione: $e->getMessage()\n";
    }
    if (($risultato->num_rows) > 0) {
        $riga = $risultato->fetch_array();
        $id = $riga[0];
        $oreMinime = $riga[6];
        $oreAutorizzate = $riga[7];
        $valore = $riga[8];

        while ($_ore < $oreMinime && $id > $idMin) {
            $id = $id - 1;
            $query = "SELECT * FROM `garaOre` where id='$id'";
            //echo $query;
            try {
                $risultato = $_conn->query($query);
            } catch (Exception $ex) {
                echo "Si è presentata la seguente eccezzione: $ex->getMessage()\n";
            }
            $riga = $risultato->fetch_array();
            $id = $riga[0];
            $oreMinime = $riga[6];
            $oreAutorizzate = $riga[7];
            $valore = $riga[8];
        }
    } else {
        $query = "SELECT * FROM `garaOre` where id='$idMin'";
        try {
            $risultato = $_conn->query($query);
        } catch (Exception $ex) {
            echo "Si è presentata la seguente eccezzione: $ex->getMessage()\n";
        }
        $riga = $risultato->fetch_array();
        $id = $riga[0];
        $oreMinime = $riga[6];
        $oreAutorizzate = $riga[7];
        $valore = $riga[8];
    }
    $risposta = [
        $oreAutorizzate,
        $valore,
    ];
    return $risposta;
}

function caricamentoImportOreHeracom($_conn) {
    $queryRicerca = "Select * from importOreHeracom";
    try {
        $risultato = $_conn->query($queryRicerca);
    } catch (Exception $e) {
        echo "Si è presentata la seguente eccezzione: $e->getMessage()\n";
    }
    while ($riga = $risultato->fetch_array()) {
        $data = date('Y-m-d', strtotime(str_replace('/', '-', $riga[6])));
            
        
        $id = $riga[0];
        $user = $riga[4];
        $nomeCompleto = $riga[1];
        $livello = $riga[5];
        $idSede = 1;
        $sede = $riga[8];
        $giorno = $riga[2];
        $mese = date('Y-m-01', strtotime($giorno));
        $userGroup = "Heracom";
        $idMandato = "Heracom";
        $mandato = "Heracom";
        $pause = 0;
        $wait = 0;
        $talk = 0;
        $dispo = 0;
        $numero = $riga[3];
        $dead = 0;
        $dataAssunzione =$data ;
        $nomeCognome = $riga[7];
        $dataImport = $giorno;
        $provenienza = "Heracom";

        $queryInserimento = "INSERT INTO `stringheTotale`"
                . "(`user`, `nomeCompleto`, `livello`, `sede`, `giorno`, `mese`, `userGroup`, `mandato`, `pause`, `wait`, `talk`, `dispo`, `numero`, `dead`, `dataAssunzione`, `NomeCognome`, `dataImport`, `provenienza`,idSede,idMandato) "
                . "VALUES ('$user','$nomeCompleto','$livello','$sede','$giorno','$mese','$userGroup','$mandato','$pause','$wait','$talk','$dispo','$numero','$dead','$dataAssunzione','$nomeCognome','$dataImport','$provenienza','$idSede','$idMandato')";
        $_conn->query($queryInserimento);
    }
    echo "fatto" . "<br>";
}

function elencoOperatori($_conn) {
    $risposta = [];
    $query = "SELECT `user`,full_name,user_level,custom_one,custom_three,territory FROM vicidial_users  ";
    $risultato = $_conn->query($query);
    while ($riga = $risultato->fetch_array()) {
        $user = $riga["user"];
        $fullName = $riga["full_name"];
        $livello = $riga["user_level"];
        $dataAssunzione = $riga["custom_one"];
        $nomeCompleto = $riga["custom_three"];
        $sede=$riga["territory"];

        $risposta[$fullName] = [$user, $livello, $dataAssunzione, $nomeCompleto,$sede];
    }
    return $risposta;
}

?>


