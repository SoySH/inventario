<?php
$host = "127.0.0.1";
$port = 3307;
$user = "root";
$pass = "";
$dbname = "inventario_db";

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    die("❌ Conexión fallida: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
