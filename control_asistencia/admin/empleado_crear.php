<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: ../login.php"); exit; }
require '../conexion.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = trim($_POST['cedula']);
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);

    if (!empty($cedula) && !empty($nombre)) {
        $codigo = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));

        $stmt = mysqli_prepare($conn, "INSERT INTO empleados (cedula, nombre, correo, telefono, codigo_acceso) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssss", $cedula, $nombre, $correo, $telefono, $codigo);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: empleados.php");
            exit;
        } else {
            $error = "La cédula ya se encuentra registrada.";
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
    <title>Registrar Empleado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h3 class="mb-4">Registrar Empleado</h3>
                        <?php if($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Cédula</label>
                                <input type="text" name="cedula" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nombre Completo</label>
                                <input type="text" name="nombre" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" name="correo" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary">Guardar Empleado</button>
                            <a href="empleados.php" class="btn btn-secondary">Cancelar</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>