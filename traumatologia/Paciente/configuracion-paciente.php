<?php
session_start();
require "../conexion.php";

if (!isset($_SESSION["usuario_id"]) || $_SESSION["usuario_tipo"] !== "paciente") {
    header("Location: ../Inicio-Registro/auth.php");
    exit;
}

$paciente_id = $_SESSION["usuario_id"];

$stmt = $conexion->prepare("SELECT * FROM pacientes WHERE id = ?");
$stmt->bind_param("i", $paciente_id);
$stmt->execute();
$result = $stmt->get_result();
$paciente = $result->fetch_assoc();

if (!$paciente) {
    session_destroy();
    header("Location: ../Inicio-Registro/auth.php");
    exit;
}

$mensaje_ok = $mensaje_error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["guardar_config"])) {
    $correo   = trim($_POST["correo"] ?? '');
    $telefono = trim($_POST["telefono"] ?? '');
    $pass     = trim($_POST["password"] ?? '');

    if ($pass !== "") {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmtUp = $conexion->prepare("UPDATE pacientes SET email = ?, telefono = ?, password = ? WHERE id = ?");
        $stmtUp->bind_param("sssi", $correo, $telefono, $hash, $paciente_id);
    } else {
        $stmtUp = $conexion->prepare("UPDATE pacientes SET email = ?, telefono = ? WHERE id = ?");
        $stmtUp->bind_param("ssi", $correo, $telefono, $paciente_id);
    }

    if ($stmtUp->execute()) {
        $mensaje_ok = "Configuración actualizada correctamente.";
        $paciente["email"]    = $correo;
        $paciente["telefono"] = $telefono;
    } else {
        $mensaje_error = "Error al actualizar la configuración.";
    }
}

$nombre   = $paciente["nombre"] ?? "";
$correo   = $paciente["email"] ?? "";
$telefono = $paciente["telefono"] ?? "";
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configuración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body>
    <!-- Loading overlay -->
    <div class="loading" id="loadingOverlay">
        <div class="spinner"></div>
    </div>
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-heart-pulse-fill me-2"></i>
                MedWeb
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-house-fill me-1"></i>Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="Analisis.php">
                            <i class="bi bi-file-medical me-1"></i>Mis Análisis
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="Citas.php">
                            <i class="bi bi-calendar-check me-1"></i>Mis Citas
                        </a>
                    </li>
                    <div class="navbar-nav">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($nombre) ?>
                        </a>
                        <ul class="dropdown-menu">
                          <li><a class="dropdown-item" href="configuracion-paciente.php"><i class="bi bi-gear me-2"></i>Configuración</a></li>
                          <li><hr class="dropdown-divider"></li>
                          <li><a class="dropdown-item" href="../Inicio-Registro/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>    
                        </ul>
                    </div>
                </div>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Configuración -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4">
                    <h4 class="mb-4 text-primary"><i class="bi bi-gear me-2"></i>Configuración de Cuenta</h4>

                    <?php if ($mensaje_ok): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($mensaje_ok) ?></div>
                    <?php endif; ?>
                    <?php if ($mensaje_error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($mensaje_error) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" value="<?= htmlspecialchars($correo) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars($telefono) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Nueva contraseña (opcional)</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <button type="submit" name="guardar_config" class="btn btn-primary w-100">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function cerrarSesion() {
            window.location.href = "../Inicio-Registro/auth.php"; 
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
