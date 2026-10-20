<?php
require 'conexion.php';
$mensaje = '';
$tipoAlerta = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = trim($_POST['cedula']);
    $codigo = trim($_POST['codigo']);

    if (!empty($cedula) && !empty($codigo)) {
        // Verificar empleado
        $stmt = mysqli_prepare($conn, "SELECT * FROM empleados WHERE cedula = ? AND codigo_acceso = ?");
        mysqli_stmt_bind_param($stmt, "ss", $cedula, $codigo);
        mysqli_stmt_execute($stmt);
        $empleado = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if ($empleado) {
            if ($empleado['estado'] === 'activo') {
                $fechaHoy = date('Y-m-d');
                $horaActual = date('H:i:s');

                // Verificar si tiene una entrada abierta hoy sin salida
                $stmtAsist = mysqli_prepare($conn, "SELECT * FROM asistencias WHERE empleado_id = ? AND fecha = ? AND hora_salida IS NULL");
                mysqli_stmt_bind_param($stmtAsist, "is", $empleado['id'], $fechaHoy);
                mysqli_stmt_execute($stmtAsist);
                $asistencia = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtAsist));

                if (!$asistencia) {
                    // Registrar Entrada
                    $stmtIns = mysqli_prepare($conn, "INSERT INTO asistencias (empleado_id, fecha, hora_entrada) VALUES (?, ?, ?)");
                    mysqli_stmt_bind_param($stmtIns, "iss", $empleado['id'], $fechaHoy, $horaActual);
                    mysqli_stmt_execute($stmtIns);
                    $mensaje = "Empleado: <strong>{$empleado['nombre']}</strong><br>Fecha: " . date('d/m/Y') . "<br>Hora de entrada: " . date('h:i A', strtotime($horaActual)) . "<br><br><span class='badge bg-success fs-6'>Entrada registrada correctamente</span>";
                    $tipoAlerta = "success";
                } else {
                    // Registrar Salida
                    $stmtUpd = mysqli_prepare($conn, "UPDATE asistencias SET hora_salida = ? WHERE id = ?");
                    mysqli_stmt_bind_param($stmtUpd, "si", $horaActual, $asistencia['id']);
                    mysqli_stmt_execute($stmtUpd);
                    $mensaje = "Empleado: <strong>{$empleado['nombre']}</strong><br>Fecha: " . date('d/m/Y') . "<br>Hora de salida: " . date('h:i A', strtotime($horaActual)) . "<br><br><span class='badge bg-info text-dark fs-6'>Salida registrada correctamente</span>";
                    $tipoAlerta = "success";
                }
            } else {
                $mensaje = "El empleado se encuentra inactivo.";
                $tipoAlerta = "danger";
            }
        } else {
            $mensaje = "Cédula o código de acceso incorrecto.";
            $tipoAlerta = "danger";
        }
    } else {
        $mensaje = "Por favor complete todos los campos.";
        $tipoAlerta = "warning";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Asistencia - Marcación</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Iconos de Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Estilos Personalizados -->
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body class="bg-dark text-white d-flex align-items-center" style="min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card glass-card text-white shadow-lg">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-clock-history display-4 text-indigo"></i>
                            <h2 class="fw-bold mt-2">Marcación de Asistencia</h2>
                            <p class="text-muted small">Ingrese su cédula y código de acceso</p>
                        </div>
                        
                        <?php if(!empty($mensaje)): ?>
                            <div class="alert alert-<?php echo $tipoAlerta; ?> alert-dismissible fade show border-0" role="alert">
                                <?php echo $mensaje; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" autocomplete="off">
                            <div class="mb-3">
                                <label class="form-label text-light fw-medium"><i class="bi bi-card-heading me-1"></i> Cédula</label>
                                <input type="text" name="cedula" class="form-control form-control-custom" placeholder="Ej: 102340567" required autofocus>
                            </div>
                            <div class="mb-4">
                                <label class="form-label text-light fw-medium"><i class="bi bi-key me-1"></i> Código de Acceso</label>
                                <input type="password" name="codigo" class="form-control form-control-custom" placeholder="Ej: A7F294" required>
                            </div>
                            <button type="submit" class="btn btn-gradient w-100 py-3 fw-bold fs-6">
                                <i class="bi bi-check-circle me-1"></i> Registrar
                            </button>
                        </form>
                        
                        <div class="text-center mt-4">
                            <a href="login.php" class="text-light text-decoration-none small opacity-75 opacity-100-hover">
                                <i class="bi bi-shield-lock me-1"></i> Acceso Administrativo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>