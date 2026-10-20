<?php
session_start();
if (!isset($_SESSION['admin_id'])) { 
    header("Location: ../login.php"); 
    exit; 
}
require '../conexion.php';

$error = '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener los datos del empleado actual
$stmt = mysqli_prepare($conn, "SELECT * FROM empleados WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$empleado = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$empleado) {
    header("Location: empleados.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = trim($_POST['cedula']);
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);
    $estado = $_POST['estado'];

    if (!empty($cedula) && !empty($nombre)) {
        // Actualizar datos del empleado
        $stmtUpd = mysqli_prepare($conn, "UPDATE empleados SET cedula = ?, nombre = ?, correo = ?, telefono = ?, estado = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmtUpd, "sssssi", $cedula, $nombre, $correo, $telefono, $estado, $id);
        
        if (mysqli_stmt_execute($stmtUpd)) {
            header("Location: empleados.php");
            exit;
        } else {
            $error = "Error al actualizar o la cédula ya pertenece a otro empleado.";
        }
    } else {
        $error = "Complete los campos obligatorios.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Empleado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h3 class="mb-4">Editar Empleado</h3>
                        <?php if($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Cédula</label>
                                <input type="text" name="cedula" class="form-control" value="<?php echo htmlspecialchars($empleado['cedula']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nombre Completo</label>
                                <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($empleado['nombre']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" name="correo" class="form-control" value="<?php echo htmlspecialchars($empleado['correo']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($empleado['telefono']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Código de Acceso (Generado Automáticamente)</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($empleado['codigo_acceso']); ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Estado</label>
                                <select name="estado" class="form-select">
                                    <option value="activo" <?php echo $empleado['estado'] === 'activo' ? 'selected' : ''; ?>>Activo</option>
                                    <option value="inactivo" <?php echo $empleado['estado'] === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Actualizar Empleado</button>
                            <a href="empleados.php" class="btn btn-secondary">Cancelar</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>