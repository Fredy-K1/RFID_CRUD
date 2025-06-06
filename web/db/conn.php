<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "rfid_users";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}

// Establecer el conjunto de caracteres a utf8mb4
$conn->set_charset("utf8mb4");
?>