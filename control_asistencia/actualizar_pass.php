<?php
require 'conexion.php';

$password_plana = '123';
$password_hash = password_hash($password_plana, PASSWORD_DEFAULT);

$stmt = mysqli_prepare($conn, "UPDATE usuarios SET password = ? WHERE usuario = 'admin'");
mysqli_stmt_bind_param($stmt, "s", $password_hash);

if (mysqli_stmt_execute($stmt)) {
    echo "<h3>¡Contraseña actualizada con éxito!</h3>";
    echo "<p>El usuario <b>admin</b> ahora tiene la contraseña: <b>123</b></p>";
    echo "<a href='login.php' style='padding: 10px 20px; background: #0d6efd; color: white; text-decoration: none; border-radius: 5px;'>Ir al Login Administrativo</a>";
} else {
    echo "Error al actualizar: " . mysqli_error($conn);
}
?>