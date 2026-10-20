<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: ../login.php"); exit; }
require '../conexion.php';

// Filtros
$where = "WHERE 1=1";
$params = [];
$types = "";

if (!empty($_GET['empleado'])) {
    $where .= " AND e.nombre LIKE ?";
    $params[] = "%" . $_GET['empleado'] . "%";
    $types .= "s";
}
if (!empty($_GET['cedula'])) {
    $where .= " AND e.cedula LIKE ?";
    $params[] = "%" . $_GET['cedula'] . "%";
    $types .= "s";
}
if (!empty($_GET['fecha'])) {
    $where .= " AND a.fecha = ?";
    $params[] = $_GET['fecha'];
    $types .= "s";
}
if (!empty($_GET['f_inicio']) && !empty($_GET['f_fin'])) {
    $where .= " AND a.fecha BETWEEN ? AND ?";
    $params[] = $_GET['f_inicio'];
    $params[] = $_GET['f_fin'];
    $types .= "ss";
}

$sql = "SELECT a.*, e.nombre, e.cedula FROM asistencias a INNER JOIN empleados e ON a.empleado_id = e.id $where ORDER BY a.fecha DESC, a.hora_entrada DESC";
$stmt = mysqli_prepare($conn, $sql);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$registros = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Asistencia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Panel Admin</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="empleados.php">Empleados</a></li>
                    <li class="nav-item"><a class="nav-link active" href="asistencias.php">Historial Asistencia</a></li>
                </ul>
                <a href="../logout.php" class="btn btn-outline-light btn-sm">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <h2>Historial de Asistencia</h2>
        
        <!-- Filtros -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Empleado</label>
                        <input type="text" name="empleado" class="form-control" value="<?php echo $_GET['empleado'] ?? ''; ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Cédula</label>
                        <input type="text" name="cedula" class="form-control" value="<?php echo $_GET['cedula'] ?? ''; ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Fecha Específica</label>
                        <input type="date" name="fecha" class="form-control" value="<?php echo $_GET['fecha'] ?? ''; ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Desde</label>
                        <input type="date" name="f_inicio" class="form-control" value="<?php echo $_GET['f_inicio'] ?? ''; ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Hasta</label>
                        <input type="date" name="f_fin" class="form-control" value="<?php echo $_GET['f_fin'] ?? ''; ?>">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-dark w-100">Filtrar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Cédula</th>
                                <th>Empleado</th>
                                <th>Fecha</th>
                                <th>Entrada</th>
                                <th>Salida</th>
                                <th>Horas trabajadas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($registros as $reg): ?>
                            <tr>
                                <td><?php echo $reg['cedula']; ?></td>
                                <td><?php echo $reg['nombre']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($reg['fecha'])); ?></td>
                                <td><?php echo $reg['hora_entrada'] ? date('h:i A', strtotime($reg['hora_entrada'])) : '-'; ?></td>
                                <td><?php echo $reg['hora_salida'] ? date('h:i A', strtotime($reg['hora_salida'])) : 'En curso'; ?></td>
                                <td>
                                    <?php 
                                        if ($reg['hora_entrada'] && $reg['hora_salida']) {
                                            $t1 = strtotime($reg['hora_entrada']);
                                            $t2 = strtotime($reg['hora_salida']);
                                            $diff = $t2 - $t1;
                                            $horas = floor($diff / 3600);
                                            $minutos = floor(($diff / 60) % 60);
                                            echo sprintf("%d:%02d", $horas, $minutos);
                                        } else {
                                            echo '-';
                                        }
                                    ?>
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