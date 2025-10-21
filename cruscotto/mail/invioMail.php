<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require '../../vendor/autoload.php'; // Carica l'autoloader di Composer

use Dompdf\Dompdf;
use Dompdf\Options;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Crea un'istanza di Dompdf
$dompdf = new Dompdf();

// Opzioni di configurazione (opzionale)
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$dompdf->setOptions($options);

$curl = curl_init("https://ssl.novadirect.it/Know/cruscotto/funzioni/creaTabella.php");


$dataMaggiore=date("Y-m-d", strtotime("-1 day"));
$mandato=["Plenitude"];
$sede=["catanzaro", "corigliano", "lamezia", "rende", "sanmarco", "sanpietro", "vibo"];

$datiInvio = [
    'dataMaggiore' => $dataMaggiore,
    'dataMinore' => date("Y-m-1", strtotime($dataMaggiore)),
    'mandato' => json_encode($mandato,true),
    'sede' => json_encode($sede,true),
    'testMode' => false,
];

curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $datiInvio);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);
//echo var_dump($response);
//echo $response;
curl_close($curl);


$html="<html><head><title>Cruscotto Ore</title><link href='../../css/tabella.css' rel='stylesheet'></head><body><header><h1>Cruscotto Ore</h1></header>";
$htmlCoda="</body></html>";
$risultato=$html.$response.$htmlCoda;
// Carica il documento HTML nel Dompdf
$dompdf->loadHtml($risultato);

// Impostazioni del formato carta e orientamento
$dompdf->setPaper('A2', 'landscape');

// Renderizza il documento PDF
$dompdf->render();
file_put_contents('cruscottoMensilePlenitude.pdf', $dompdf->output());

// Salva il PDF su disco
//$dompdf->stream('documento.pdf');
$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
try {
    //Server settings
//    $mail->SMTPDebug = 2;                                 // Enable verbose debug output

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtps.aruba.it';                       // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'bruno.cosentino@novaholding.it';                 // SMTP username
    $mail->Password = 'Sleecose4!';                           // SMTP password
    $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = '465';                                    // TCP port to connect to    
//Recipients
    $mail->SetFrom('bruno.cosentino@novaholding.it');

    $mail->addAddress('bruno.cosentino@novaholding.it');               // Name is optional
    $mail->addReplyTo('bruno.cosentino@novaholding.it', 'Per Informazioni');
    $mail->addAddress('bruno.cosentino@novaholding.it');
    //$mail->addAddress('marco.scoppetta@novaholding.it');

    //$mail->addBCC('andrea.gambardella@vivienergia.it');
    $mail->addAttachment("cruscottoMensilePlenitude.pdf");

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = "Cruscotto";
    $mail->Body = "Prova di Invio";
    //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    $mail->send();
    echo 'Il messaggio è stato inviato';
} catch (Exception $e) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
}