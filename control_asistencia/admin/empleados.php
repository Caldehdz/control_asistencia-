<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: ../login.php"); exit; }
require '../conexion.php';

// Cambiar estado activo/inactivo
if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $stmt = mysqli_prepare($conn, "UPDATE empleados SET estado = IF(estado='activo', 'inactivo', 'activo') WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    header("Location: empleados.php");
    exit;
}

$resultado = mysqli_query($conn, "SELECT * FROM empleados ORDER BY id DESC");
$empleados = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administración de Empleados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Panel Admin</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link active" href="empleados.php">Empleados</a></li>
                    <li class="nav-item"><a class="nav-link" href="asistencias.php">Historial Asistencia</a></li>
                </ul>
                <a href="../logout.php" class="btn btn-outline-light btn-sm">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Lista de Empleados</h2>
            <a href="empleado_crear.php" class="btn btn-primary">Registrar Nuevo Empleado</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Cédula</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Teléfono</th>
                                <th>Código</th>
                                <th>Estado</th>
                                <th>Fecha Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($empleados as $emp): ?>
                            <tr>
                                <td><?php echo $emp['cedula']; ?></td>
                                <td><?php echo $emp['nombre']; ?></td>
                                <td><?php echo $emp['correo']; ?></td>
                                <td><?php echo $emp['telefono']; ?></td>
                                <td><code><?php echo $emp['codigo_acceso']; ?></code></td>
                                <td>
                                    <span class="badge bg-<?php echo $emp['estado'] == 'activo' ? 'success' : 'danger'; ?>">
                                        <?php echo ucfirst($emp['estado']); ?>
                                    </span>
                                </td>
                                <td><?php echo $emp['fecha_registro']; ?></td>
                                <td>
                                    <a href="empleado_editar.php?id=<?php echo $emp['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="empleados.php?toggle=<?php echo $emp['id']; ?>" class="btn btn-sm btn-<?php echo $emp['estado'] == 'activo' ? 'secondary' : 'success'; ?>">
                                        <?php echo $emp['estado'] == 'activo' ? 'Desactivar' : 'Activar'; ?>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>