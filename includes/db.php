<?php
$servername = "localhost";
$username = "parkinguser";
$password = "1234";
$dbname = "parking";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
