<?php
// Conexion a la base de datos - Docker/Fly.io
$host = '127.0.0.1';
$db   = 'sistema_agencia';
$user = 'agencia_user';
$pass = 'agencia_pass2026';
$charset = 'utf8mb4';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Error de conexion: ' . $conn->connect_error);
}

$conn->set_charset($charset);
?>
