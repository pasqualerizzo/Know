<?php
$servername = "localhost";
$username = "root";
$password = "qv9P@sHS6BK*O$";
$db = "know";
$db_port = "3306";
try {
$conn = new PDO("mysql:host=$servername;port=$db_port;dbname=$db", $username, $password);
// set the PDO error mode to exception
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//echo "Connected successfully";
}
catch(PDOException $e)
{
echo "Connection failed: " . $e->getMessage();
}
?>