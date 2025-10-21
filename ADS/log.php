<?php
//function log

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function log_action($error,$conn){
    $DateAndTime = date('d-m-Y h:i:s a', time());
    $data = [$error,$DateAndTime];
    $sql ="INSERT INTO log(note, dataOra) VALUES (?,?)";
    $stmt = $conn->prepare($sql);
try {
    $conn->beginTransaction();
    $stmt->execute($data);
    $conn->commit();
}catch (Exception $e){
    $conn->rollback();
    throw $e;
}

}