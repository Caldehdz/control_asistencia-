<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}
require '../conexion.php';

$hoy = date('Y-m-d');

// Consultas para el Dashboard con MySQLi
$totalEmpleados = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM empleados"))[0];
$empleadosActivos = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM empleados WHERE estado = 'activo'"))[0];
$ingresaronHoy = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(DISTINCT empleado_id) FROM asistencias WHERE fecha = '$hoy'"))[0];
$dentroAhora = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM asistencias WHERE fecha = '$hoy' AND hora_salida IS NULL"))[0];
$registrosHoy = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM asistencias WHERE fecha = '$hoy'"))[0];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Control de Asistencia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Panel Admin</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="empleados.php">Empleados</a></li>
                    <li class="nav-item"><a class="nav-link" href="asistencias.php">Historial Asistencia</a></li>
                </ul>
                <span class="navbar-text me-3">Hola, <?php echo $_SESSION['admin_nombre']; ?></span>
                <a href="../logout.php" class="btn btn-outline-light btn-sm">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="mb-4">Dashboard</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card text-bg-primary shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Total de Empleados</h5>
                        <h3><?php echo $totalEmpleados; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-success shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Empleados Activos</h5>
                        <h3><?php echo $empleadosActivos; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-info shadow-sm text-white">
                    <div class="card-body">
                        <h5 class="card-title">Personas que ingresaron hoy</h5>
                        <h3><?php echo $ingresaronHoy; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-bg-warning shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Personas actualmente dentro</h5>
                        <h3><?php echo $dentroAhora; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-bg-secondary shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Registros realizados hoy</h5>
                        <h3><?php echo $registrosHoy; ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>