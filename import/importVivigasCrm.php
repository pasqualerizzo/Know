<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "../connessione/connessioneCrm.php";
require "../connessione/connessioneCrmNuovo.php";
require "../funzioni/funzioniCrm.php";

$objCrm = new ConnessioneCrm();
$connCrm = $objCrm->apriConnessioneCrm();

$objCrmN = new ConnessioneCrmNuovo();
$connCrmNuovo = $objCrmN->apriConnessioneCrmNuovo();

$queryOrigine = "
SELECT 
operatore.user_name as 'operatore',
cf.cf_5468 AS assegnatoasub,
cf.cf_4130 AS assegnatominipda,
e.smownerid AS assignedto,
cf.cf_5458 AS bollettawebsub,
cf.cf_1840 AS capfatturazione,
cf.cf_1802 AS capfornitura,
cf.cf_1694 AS capresidenza,
cf.cf_1888 AS capsedelegale,
cf.cf_1732 AS causaleannullamento,
cf.cf_1804 AS cellulare,
cf.cf_1860 AS codicecampagna,
cf.cf_1862 AS codicefatturaelettronica,
cf.cf_1700 AS codicefiscale,
cf.cf_1782 AS codicefiscaleintestatario,
cf.cf_3757 AS codicefornituragas,
cf.cf_3759 AS codicefornituraluce,
cf.cf_1758 AS codicepdr,
cf.cf_1672 AS cognomeclienteragionesociale,
cf.cf_1806 AS commodity,
cf.cf_5464 AS commoditysub,
cf.cf_1842 AS comunefatturazione,
cf.cf_1798 AS comunefornitura,
cf.cf_1696 AS comuneresidenza,
cf.cf_1890 AS comunesedelegale,
cf.cf_1772 AS consensoinviocomunicazioniamezzoemail,
cf.cf_1770 AS consensoinviocomunicazioniamezzopostacartace,
cf.cf_1774 AS consensoinviocomunicazioniamezzotelefono,
cf.cf_2961 AS consensorisparmiami,
cf.cf_1776 AS consensotrattamentodatipersonaliperfinalitàd,
cf.cf_1750 AS consumoannuo,
cf.cf_1766 AS consumomqannuo,
e.createdtime AS createdtime,
cf.cf_1676 AS datadinascita,
cf.cf_5414 AS datafirmacontratto,
cf.cf_1742 AS datainserimento,
cf.cf_1852 AS datarilasciodocumenti,
cf.cf_1812 AS datasottoscrizionecontratto,
cf.cf_4867 AS dataswitchingas,
cf.cf_4863 AS dataswitchinluce,
cf.cf_4869 AS dataswitchoutgas,
cf.cf_4865 AS dataswitchoutluce,
cf.cf_1820 AS dataswitchingrichiesta,
cf.cf_3266 AS efficientamentoenergetico,
cf.cf_4104 AS firmaelettronica,
cf.cf_1764 AS fornitoregas,
cf.cf_1754 AS fornitoreluce,
cf.cf_5462 AS hlrplenisub,
cf.cf_3881 AS hlrvivigas,
cf.cf_1710 AS iban,

cf.cf_1736 AS idrecqualitycall,
cf.cf_1734 AS idrecvocalopt,
cf.cf_1722 AS idrigagas,
cf.cf_1720 AS idrigaluce,
cf.cf_4132 AS idsponsorizzate,
cf.cf_2600 AS interessatotelefonia,
cf.cf_1834 AS internofatturazione,
cf.cf_1796 AS internofornitura,
cf.cf_1692 AS internoresidenza,
cf.cf_1882 AS internosedelegale,
cf.cf_3264 AS inviomail,
cf.cf_3877 AS invioprimamail,
cf.cf_1866 AS inviosecondamail,
e.modifiedby AS lastmodifiedby,
cf.cf_2978 AS leadidvici,
cf.cf_1678 AS luogodinascita,
cf.cf_1706 AS mail,
cf.cf_1848 AS mercato,
cf.cf_1756 AS mercatodiprovenienza,
cf.cf_1778 AS metododipagamento,
cf.cf_5460 AS metododipagamentosub,
cf.cf_1822 AS modalitàcambio,
cf.cf_1824 AS modalitàdicambio,
e.modifiedtime AS modifiedtime,
cf.cf_1894 AS motivazioneko,
cf.cf_1674 AS nomecliente,
cf.cf_5470 AS nomecognometitolaresub,
cf.cf_1780 AS nomeintestatario,
cf.cf_1716 AS notebackoffice,
cf.cf_1714 AS noteoperatore,
cf.cf_3753 AS notestatogas,
cf.cf_3755 AS notestatoluce,
cf.cf_1830 AS numerocivicofatturazione,
cf.cf_1788 AS numerocivicofornitura,
cf.cf_1684 AS numerocivicoresidenza,
cf.cf_1878 AS numerocivicosedelegale,
cf.cf_3346 AS numeroconsensatopersfa,
cf.cf_1850 AS numerodocumento,
cf.cf_4027 AS operatoretelemarketing,
cf.cf_1702 AS partitaiva,
cf.cf_1872 AS pec,
cf.cf_3761 AS pesolordo,
cf.cf_3765 AS pesopagato,
cf.cf_1836 AS pianofatturazione,
cf.cf_1794 AS pianofornitura,
cf.cf_1690 AS pianoresidenza,
cf.cf_1884 AS pianosedelegale,
cf.cf_1746 AS pod,
cf.cf_1748 AS potimp,
cf.cf_1844 AS provinciafatturazione,
cf.cf_1800 AS provinciafornitura,
cf.cf_1698 AS provinciaresidenza,
cf.cf_1892 AS provinciasedelegale,
cf.cf_5476 AS residentesub,
cf.cf_1864 AS rilasciatoda,
cf.cf_1814 AS rinunciadirittorecesso,
cf.cf_1896 AS sanatabo,
cf.cf_1838 AS scalafatturazione,
cf.cf_1792 AS scalafornitura,
cf.cf_1688 AS scalaresidenza,
cf.cf_1886 AS scalasedelegale,
cf.cf_1810 AS sede,
cf.cf_5456 AS sedesub,
e.source AS source,
cf.cf_1708 AS spedizionebolletta,

cf.cf_1718 AS statopda,
cf.cf_1726 AS statorigagas,
cf.cf_1724 AS statorigaluce,
cf.cf_1832 AS suffissofatturazione,
cf.cf_1790 AS suffissofornitura,
cf.cf_1686 AS suffissoresidenza,
cf.cf_1880 AS suffissosedelegale,

cf.cf_1762 AS tariffagas,
cf.cf_5474 AS tariffagassub,
cf.cf_1760 AS tariffaluce,
cf.cf_5472 AS tariffalucesub,
cf.cf_1704 AS telefono,
cf.cf_5466 AS tipoacquisizionesub,
cf.cf_1856 AS tipodocumento,
cf.cf_1870 AS tipoiban,
cf.cf_1752 AS tipologia,
cf.cf_1738 AS titolaritàimmobile,
cf.cf_1826 AS toponimofatturazione,
cf.cf_1784 AS toponimofornitura,
cf.cf_1680 AS toponimoresidenza,
cf.cf_1874 AS toponimosedelegale,
cf.cf_1768 AS utilizzo,
cf.cf_4879 AS utmcampagna,
cf.cf_1828 AS viafatturazione,
cf.cf_1786 AS viafornitura,
cf.cf_1682 AS viaresidenza,
cf.cf_1876 AS viasedelegale,
c.vivigasno AS vivigasno,
cf.cf_1808 AS winback,
cf.cf_1868 AS zonagas

FROM 
vtiger_vivigascf  as cf
inner join vtiger_vivigas as c ON c.vivigasid=cf.vivigasid
inner join vtiger_crmentity as e ON c.vivigasid=e.crmid 
inner join vtiger_users as operatore on e.smownerid=operatore.id 
WHERE
e.deleted=0 and cf.cf_1812 BETWEEN '2025-06-01' AND '2025-06-06'
";

$risposta = $connCrm->query($queryOrigine);
while ($riga = $risposta->fetch_array()) {
    $operatore = $riga['operatore'];
    $assegnatoasub = $riga['assegnatoasub'];
    $assegnatominipda = $riga['assegnatominipda'];
    $assignedto = $riga['assignedto'];
    $bollettawebsub = $riga['bollettawebsub'];
    $capfatturazione = $riga['capfatturazione'];
    $capfornitura = $riga['capfornitura'];
    $capresidenza = $riga['capresidenza'];
    $capsedelegale = $riga['capsedelegale'];
    $causaleannullamento = $riga['causaleannullamento'];
    $cellulare = $riga['cellulare'];
    $codicecampagna = $riga['codicecampagna'];
    $codicefatturaelettronica = $riga['codicefatturaelettronica'];
    $codicefiscale = $riga['codicefiscale'];
    $codicefiscaleintestatario = $riga['codicefiscaleintestatario'];
    $codicefornituragas = $riga['codicefornituragas'];
    $codicefornituraluce = $riga['codicefornituraluce'];
    $codicepdr = $riga['codicepdr'];
    $cognomeclienteragionesociale = $riga['cognomeclienteragionesociale'];
    $commodity = $riga['commodity'];
    $commoditysub = $riga['commoditysub'];
    $comunefatturazione = $riga['comunefatturazione'];
    $comunefornitura = $riga['comunefornitura'];
    $comuneresidenza = $riga['comuneresidenza'];
    $comunesedelegale = $riga['comunesedelegale'];
    $consensoinviocomunicazioniamezzoemail = $riga['consensoinviocomunicazioniamezzoemail'];
    $consensoinviocomunicazioniamezzopostacartace = $riga['consensoinviocomunicazioniamezzopostacartace'];
    $consensoinviocomunicazioniamezzotelefono = $riga['consensoinviocomunicazioniamezzotelefono'];
    $consensorisparmiami = $riga['consensorisparmiami'];
    $consensotrattamentodatipersonaliperfinalitàd = $riga['consensotrattamentodatipersonaliperfinalitàd'];
    $consumoannuo = $riga['consumoannuo'];
    $consumomqannuo = $riga['consumomqannuo'];
    $createdtime = $riga['createdtime'];
    $datadinascita = $riga['datadinascita'];
    $datafirmacontratto = $riga['datafirmacontratto'];
    $datainserimento = $riga['datainserimento'];
    $datarilasciodocumenti = $riga['datarilasciodocumenti'];
    $datasottoscrizionecontratto = $riga['datasottoscrizionecontratto'];
    $dataswitchingas = $riga['dataswitchingas'];
    $dataswitchinluce = $riga['dataswitchinluce'];
    $dataswitchoutgas = $riga['dataswitchoutgas'];
    $dataswitchoutluce = $riga['dataswitchoutluce'];
    $dataswitchingrichiesta = $riga['dataswitchingrichiesta'];
    $efficientamentoenergetico = $riga['efficientamentoenergetico'];
    $firmaelettronica = $riga['firmaelettronica'];
    $fornitoregas = $riga['fornitoregas'];
    $fornitoreluce = $riga['fornitoreluce'];
    $hlrplenisub = $riga['hlrplenisub'];
    $hlrvivigas = $riga['hlrvivigas'];
    $iban = $riga['iban'];

    $idrecqualitycall = $riga['idrecqualitycall'];
    $idrecvocalopt = $riga['idrecvocalopt'];
    $idrigagas = $riga['idrigagas'];
    $idrigaluce = $riga['idrigaluce'];
    $idsponsorizzate = $riga['idsponsorizzate'];
    $interessatotelefonia = $riga['interessatotelefonia'];
    $internofatturazione = $riga['internofatturazione'];
    $internofornitura = $riga['internofornitura'];
    $internoresidenza = $riga['internoresidenza'];
    $internosedelegale = $riga['internosedelegale'];
    $inviomail = $riga['inviomail'];
    $invioprimamail = $riga['invioprimamail'];
    $inviosecondamail = $riga['inviosecondamail'];
    $lastmodifiedby = $riga['lastmodifiedby'];
    $leadidvici = $riga['leadidvici'];
    $luogodinascita = $riga['luogodinascita'];
    $mail = $riga['mail'];
    $mercato = $riga['mercato'];
    $mercatodiprovenienza = $riga['mercatodiprovenienza'];
    $metododipagamento = $riga['metododipagamento'];
    $metododipagamentosub = $riga['metododipagamentosub'];
    $modalitàcambio = $riga['modalitàcambio'];
    $modalitàdicambio = $riga['modalitàdicambio'];
    $modifiedtime = $riga['modifiedtime'];
    $motivazioneko = $riga['motivazioneko'];
    $nomecliente = $riga['nomecliente'];
    $nomecognometitolaresub = $riga['nomecognometitolaresub'];
    $nomeintestatario = $riga['nomeintestatario'];
    $notebackoffice = $riga['notebackoffice'];
    $noteoperatore = $riga['noteoperatore'];
    $notestatogas = $riga['notestatogas'];
    $notestatoluce = $riga['notestatoluce'];
    $numerocivicofatturazione = $riga['numerocivicofatturazione'];
    $numerocivicofornitura = $riga['numerocivicofornitura'];
    $numerocivicoresidenza = $riga['numerocivicoresidenza'];
    $numerocivicosedelegale = $riga['numerocivicosedelegale'];
    $numeroconsensatopersfa = $riga['numeroconsensatopersfa'];
    $numerodocumento = $riga['numerodocumento'];
    $operatoretelemarketing = $riga['operatoretelemarketing'];
    $partitaiva = $riga['partitaiva'];
    $pec = $riga['pec'];
    $pesolordo = $riga['pesolordo'];
    $pesopagato = $riga['pesopagato'];
    $pianofatturazione = $riga['pianofatturazione'];
    $pianofornitura = $riga['pianofornitura'];
    $pianoresidenza = $riga['pianoresidenza'];
    $pianosedelegale = $riga['pianosedelegale'];
    $pod = $riga['pod'];
    $potimp = $riga['potimp'];
    $provinciafatturazione = $riga['provinciafatturazione'];
    $provinciafornitura = $riga['provinciafornitura'];
    $provinciaresidenza = $riga['provinciaresidenza'];
    $provinciasedelegale = $riga['provinciasedelegale'];
    $residentesub = $riga['residentesub'];
    $rilasciatoda = $riga['rilasciatoda'];
    $rinunciadirittorecesso = $riga['rinunciadirittorecesso'];
    $sanatabo = $riga['sanatabo'];
    $scalafatturazione = $riga['scalafatturazione'];
    $scalafornitura = $riga['scalafornitura'];
    $scalaresidenza = $riga['scalaresidenza'];
    $scalasedelegale = $riga['scalasedelegale'];
    $sede = $riga['sede'];
    $sedesub = $riga['sedesub'];
    $source = $riga['source'];
    $spedizionebolletta = $riga['spedizionebolletta'];

    $statopda = $riga['statopda'];
    $statorigagas = $riga['statorigagas'];
    $statorigaluce = $riga['statorigaluce'];
    $suffissofatturazione = $riga['suffissofatturazione'];
    $suffissofornitura = $riga['suffissofornitura'];
    $suffissoresidenza = $riga['suffissoresidenza'];
    $suffissosedelegale = $riga['suffissosedelegale'];

    $tariffagas = $riga['tariffagas'];
    $tariffagassub = $riga['tariffagassub'];
    $tariffaluce = $riga['tariffaluce'];
    $tariffalucesub = $riga['tariffalucesub'];
    $telefono = $riga['telefono'];
    $tipoacquisizionesub = $riga['tipoacquisizionesub'];
    $tipodocumento = $riga['tipodocumento'];
    $tipoiban = $riga['tipoiban'];
    $tipologia = $riga['tipologia'];
    $titolaritàimmobile = $riga['titolaritàimmobile'];
    $toponimofatturazione = $riga['toponimofatturazione'];
    $toponimofornitura = $riga['toponimofornitura'];
    $toponimoresidenza = $riga['toponimoresidenza'];
    $toponimosedelegale = $riga['toponimosedelegale'];
    $utilizzo = $riga['utilizzo'];
    $utmcampagna = $riga['utmcampagna'];
    $viafatturazione = $riga['viafatturazione'];
    $viafornitura = $riga['viafornitura'];
    $viaresidenza = $riga['viaresidenza'];
    $viasedelegale = $riga['viasedelegale'];
    $vivigasno = $riga['vivigasno'];
    $winback = $riga['winback'];
    $zonagas = $riga['zonagas'];

    $queryOperatore = " SELECT id FROM vtiger_users WHERE user_name='$operatore'";
    $risp = $connCrmNuovo->query($queryOperatore);
    if ($risp->num_rows > 0) {
        $op = $risp->fetch_array();
        $idOperatore = $op[0];
    } else {
        $idOperatore = 5;
    }


    $dati = [
    'vivigas'=>$vivigasno,
   
    'leadid'=>$leadidvici,
    'idsponsorizzata'=>$idsponsorizzate,
    'winback'=>$winback,
    'utm'=>$utmcampagna,
    
   
    'codicecampagna'=>$codicecampagna,
    
    'sede'=>$sede,
    
 
    'datasottoscrizionecontratto'=>$datasottoscrizionecontratto,
    'commodity'=>$commodity,
    'mercato'=>$mercato,
    
    
    
    
    'nome'=>$nomecliente,
    'cognome'=>$cognomeclienteragionesociale,
    'ragionesociale'=>$cognomeclienteragionesociale,
    
    'luogonascita'=>$luogodinascita,
    
    'datanascita'=>$datadinascita,
    'codicefiscale'=>$codicefiscale,
    'partitaiva'=>$partitaiva,
    'cellulareprimario'=>$cellulare,
    'telefono'=>$telefono,
    
    'mail'=>$mail,
    'numerodocumento'=>$numerodocumento,
        
    'enterilasciodocumento'=>$rilasciatoda,
    'datarilasciodocumento'=>$datarilasciodocumenti,
    
    'indirizzofornitura'=>$comunefornitura,
    'provinciafornitura'=>$provinciafornitura,
    'capfornitura'=>$capfornitura,
    'comunefornitura'=>$comunefornitura,
    'indirizzoresidenza'=>$comuneresidenza,
    'provinciaresidenza'=>$provinciaresidenza,
    'capresidenza'=>$capresidenza,
    'comuneresidenza'=>$comuneresidenza,
    'indirizzofatturazione'=>$comunefatturazione,
    'provinciafatturazione'=>$provinciafatturazione,
    'capfatturazione'=>$capfatturazione,
    'comunefatturazione'=>$capfatturazione,
    'pod'=>$pod,
    'tariffaluce'=>$tariffaluce,
    'fornitoreenergiaelettrica'=>$fornitoreluce,
    'consumoenergiaelettrica'=>$consumoannuo,
    'potenzaimpianto'=>$potimp,
    'codicepdr'=>$codicepdr,
    'tariffagas'=>$tariffagas,
    'fornitoregas'=>$fornitoregas,
    'consumoannuogas'=>$consumomqannuo,
    
    
    'metodopagamento'=>$metododipagamento,
    'iban'=>$iban,
    'ragionesocialetitolare'=>$nomeintestatario,
    'codicefiscaletitolare'=>$codicefiscaleintestatario,
    'metodoinviofattura'=>$spedizionebolletta,
    'noteoperatore'=>$noteoperatore,
    'notebackoffice'=>$notebackoffice,
    'statopda'=>$statopda,
    'motivazioneko'=>$motivazioneko,
    'codiceplicoluce'=>$idrigaluce,
    'statoplicoluce'=>$statorigaluce,
    'noteplicoluce'=>$notestatoluce,
    'codiceplicogas'=>$idrigagas,
    'statoplicogas'=>$statorigagas,
    'noteplicogas'=>$notestatogas,

    'datacontratto'=>$datasottoscrizionecontratto,
        'dataswitchingrichiesta'=>$dataswitchingrichiesta,
    ];

    $r = importModulo($dati, $idOperatore, "Vivigas");
    echo $r . "<br>";
}