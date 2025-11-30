<?php
session_start();
require "../conexion.php";

if (!isset($_SESSION["usuario_id"]) || $_SESSION["usuario_tipo"] !== "paciente") {
    header("Location: ../Inicio-Registro/auth.php");
    exit;
}

$id = $_SESSION["usuario_id"];

// Obtener nombre del paciente
$stmt = $conexion->prepare("SELECT nombre FROM pacientes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$nombre = $user["nombre"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Citas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .main-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,.1);
            margin-bottom: 2rem;
        }
        .header-section {
            background-color: #0d6efd;
            color: white;
            padding: 1.5rem;
            border-radius: 8px 8px 0 0;
            display: flex;
            justify-content: between;
            align-items: center;
        }
        .content-section {
            padding: 1.5rem;
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
                    <a class="nav-link active" href="Citas.php">
                        <i class="bi bi-calendar-check me-1"></i>Mis Citas
                    </a>
                </li>

                <!-- Nombre del paciente -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($nombre) ?>
                    </a>

                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="configuracion-paciente.php"><i class="bi bi-gear me-2"></i>Configuración</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../Inicio-Registro/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
                    </ul>
                </li>

            </ul>

        </div>

    </div>
</nav>

<!-- CONTENIDO ORIGINAL SIN CAMBIOS -->
<div class="container mt-4">

    <div class="main-container">
        <div class="header-section">
            <div class="d-flex justify-content-between align-items-center w-100">
                <div>
                    <h2 class="h4 mb-0">
                        <i class="bi bi-calendar-check me-2"></i>Mis Citas
                    </h2>
                    <p class="mb-0 mt-1 opacity-75">Gestiona y consulta tus citas médicas</p>
                </div>
                <button class="btn btn-light" onclick="window.location.href='nueva-cita.php'">
                    <i class="bi bi-plus-lg me-1"></i>Nueva Cita
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Próximas Citas -->
        <div class="col-lg-8">
            <div class="main-container">
                <div class="header-section">
                    <h3 class="h5 mb-0">
                        <i class="bi bi-clock me-2"></i>Próximas Citas
                    </h3>
                </div>
                <div class="content-section">
                    <div class="list-group">

                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="mb-0">Consulta de seguimiento</h5>
                                        <span class="badge bg-primary">Confirmada</span>
                                    </div>
                                    <p class="mb-1 text-muted">
                                        <i class="bi bi-person-badge me-1"></i>Dr. Luis Rodríguez
                                    </p>
                                    <p class="mb-2 text-muted">
                                        <i class="bi bi-geo-alt me-1"></i>Clínica Central
                                    </p>
                                    <p class="mb-2 text-primary fw-bold">
                                        <i class="bi bi-calendar3 me-1"></i>25 de Marzo, 2024 - 10:30 AM
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i>Detalles
                                </button>
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-x-circle me-1"></i>Cancelar
                                </button>
                            </div>
                        </div>

                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Recordatorio:</strong> Llega 15 minutos antes.
                    </div>

                </div>
            </div>
        </div>

        <!-- Historial -->
        <div class="col-lg-4">
            <div class="main-container">
                <div class="header-section">
                    <h3 class="h5 mb-0">
                        <i class="bi bi-journal-medical me-2"></i>Historial
                    </h3>
                </div>
                <div class="content-section">
                    <div class="list-group">

                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Consulta inicial</h6>
                                <small class="text-muted">15/03/2024</small>
                            </div>
                            <span class="badge bg-success mt-1">Completada</span>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<footer class="footer py-3 bg-light">
    <div class="container text-center">
        <span class="text-muted">© 2024 MedWeb - Todos los derechos reservados</span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
