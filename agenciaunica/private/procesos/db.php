<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = getenv('DB_HOST')     ?: '127.0.0.1';
$username   = getenv('DB_USER')     ?: 'agencia_user';
$password   = getenv('DB_PASSWORD') ?: 'agencia_pass';
$dbname     = getenv('DB_NAME')     ?: 'agencia';

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
