<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
session_start();

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require_once './invioCrm.php';
// apertura file
$csv = $_FILES['importCSVOperatori'];
$nomeFile = $csv['tmp_name'];
$sizeFile = $csv['size'];

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$cont = 0;
$err = 0;
$saltato = 0;
$vuoto = 0;

$lista = "";
$campagna = "";
$leadId = "INVALID";

if (($file = fopen($nomeFile, "r")) !== false) {
    while (($riga = fgetcsv($file, $sizeFile, ";")) !== false) {
        if ($cont == 0) {
            $cont++;
        } else {
            $cont++;
            $nome = $riga[0];
            $mail = $riga[1];
            $telefono = str_replace([' ', "'+39"], '', $riga[2]);
            $roleName = $riga[8];
            $source = 'INDEED';
            $ruolo = $riga[7];
            $dataRichiesta = $riga[9];
            $utmSource = 'INDEED';
            $utmCampaign = $riga[7];
            $utmMedium = $riga[7];

            $esperienza = "-";
            $pc = "0";
            $lingua = "0";
            $cv = "-";

            if (strpos($roleName, "Lamezia") !== false) {
                $sede = "Lamezia Terme";
            } elseif (strpos($roleName, "San Pietro") !== false) {
                $sede = "SanPietro";
            } elseif (strpos($roleName, "San_Pietro") !== false) {
                $sede = "SanPietro";
            } elseif (strpos($roleName, "Rende") !== false) {
                $sede = "Rende";
            } elseif (strpos($roleName, "Rende 2") !== false) {
                $sede = "Rende 2";
            } elseif (strpos($roleName, "Castrovillari") !== false) {
                $sede = "Castrovillari";
            } elseif (strpos($roleName, "Vibo") !== false) {
                $sede = "Vibo Valentia";
            } elseif (strpos($roleName, "Catanzaro") !== false) {
                $sede = "Catanzaro";
            }

            switch ($sede) {
                case "Lamezia Terme":
                    $lista = "8110";
                    $campagna = "HR_Lam";
                    break;
                case "Rende":
                case "montalto uffugo":
                    $lista = "8210";
                    $campagna = "HR_Rnd";
                    break;
                case "Catanzaro":
                    $lista = "8810";
                    $campagna = "HR_CZ";
                    break;
                case "Corigliano Rossano":
                    $lista = "8610";
                    $campagna = "HR_Cor";
                    break;
                case "Vibo Valentia":
                    $lista = "8310";
                    $campagna = "HR_VV";
                    break;
                case "San Marco Argentano":
                    $lista = "8510";
                    $campagna = "HR_SM";
                    break;
                case "San Pietro a Maida":
                    $lista = "8410";
                    $campagna = "HR_SP";
                    break;
                case "Castrovillari":
                    $lista = "8710";
                    $campagna = "HR_Cas";
                    break;
            }


            $query_fields = [
                'user' => "apiuserid",
                'pass' => "apipass",
                'source' => $source,
                'list_id' => $lista,
                'campaign_id' => $campagna,
                'function' => "add_lead",
                'first_name' => $nome,
                'last_name' => $nome,
                'phone_number' => $telefono,
                'add_to_hopper' => "Y",
                'address1' => $dataRichiesta,
                'address3' => $source,
                'city' => $sede,
                'email' => $mail,
            ];
            $url = "https://siscall2.novadirect.it/vicidial/non_agent_api.php";
            $curl = curl_init($url . "?" . http_build_query($query_fields));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            //echo $response;

            if (strpos($response, "SUCCESS") === 0) {
                
                $ricerca = "|" . $lista . "|";
                $listaPosizione = strpos($response, $ricerca) + strlen($ricerca);
                $finePosizione = strpos($response, "|", $listaPosizione);
                $lunghezza = $finePosizione - $listaPosizione;
                $leadId = substr($response, $listaPosizione, $lunghezza);

                importRecruitingUTMLogCv($leadId, $nome, $mail, $sede, $source, $ruolo, $dataRichiesta, $esperienza, $pc, $lingua, $utmSource, $utmCampaign, $utmMedium, $response, $cv);
            }else{
                $err++;
            }
            $dataModificata = date('Y-m-d', strtotime($dataRichiesta));
            $query = "INSERT INTO `log_recruiting`( `source`, `sede`, `nome`, `telefono`, `mail`, `leadId`, `ruolo`, `data`, `info`,dataConfronto,pc,lingua,utmSource,utmCampaign,utmMedium,cv) VALUES ('$source','$sede','$nome','$telefono','$mail','$leadId','$ruolo','$dataRichiesta','$response','$dataModificata','$pc','$lingua','$utmSource','$utmCampaign','$utmMedium','$cv')";
            $conn19->query($query);

            curl_close($curl);
        }
    }
}
?>
<html>
        <head>
            <meta charset="UTF-8">

            <title>Import Operatore</title>
        </head>
        <body>

            <form name="home" action="../index.php" method="POST">
                <label for="conteggio">Conteggio</label>
                <input type="text" id="conteggio" name="conteggio" value=<?php echo $cont - 1; ?> readonly>
                <br>
               
                <label for="errore">Non Importati</label>
                <input type="text" id="errore" name="errore" value=<?php echo $err; ?> readonly>
                <br>
           
                        
            <input type="submit" value="Nuovo Import" name="nuovoImport" />
        </form>

    </body>
</html>



