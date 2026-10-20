<?php
// Configurar la zona horaria correcta 
date_default_timezone_set('America/Costa_Rica');
// Datos de conexión.
$host = "localhost";
$usuario = "root";
$contrasena = "";
$base_datos = "control_asistencia";

// Crear conexión usando MySQLi.
$conn = mysqli_connect($host, $usuario, $contrasena, $base_datos);

// Verificar si ocurrió un error.
if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Trabajar con UTF-8.
mysqli_set_charset($conn, "utf8mb4");

// La variable $conn queda disponible para los archivos que incluyan este archivo.
?>