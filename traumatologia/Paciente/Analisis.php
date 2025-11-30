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
    <title>Mis Análisis</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../css/styles.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .card {
            border-radius: 10px;
        }
        .table th {
            background-color: #007bff;
            color: white;
        }
        .table-hover tbody tr:hover {
            background-color: #e9ecef;
        }
        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
        }
        .footer {
            background-color: #f8f9fa;
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
                    <a class="nav-link active" href="Analisis.php">
                        <i class="bi bi-file-medical me-1"></i>Mis Análisis
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="Citas.php">
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

<!-- CONTENIDO ORIGINAL (NO CAMBIADO) -->
<div class="container my-5">
    <h1 class="display-4 text-center">Mis Análisis</h1>
    <div class="d-flex justify-content-center mb-4">
        <a href="nuevo-analisis.php" class="btn btn-success btn-lg">
            <i class="bi bi-plus-lg"></i> Solicitar Análisis
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Doctor</th>
                            <th>Resultados</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>15/03/2023</td>
                            <td>Radiografía de rodilla</td>
                            <td>Dr. Rodríguez</td>
                            <td><span class="badge bg-success">Disponible</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewAnalysisModal">Ver</button>
                                <button class="btn btn-sm btn-outline-secondary">Descargar</button>
                            </td>
                        </tr>
                        <!-- Más análisis... -->
                    </tbody>
                </table>
            </div>

            <nav class="mt-3">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled"><a class="page-link">Anterior</a></li>
                    <li class="page-item active"><a class="page-link">1</a></li>
                    <li class="page-item"><a class="page-link">2</a></li>
                    <li class="page-item"><a class="page-link">Siguiente</a></li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="viewAnalysisModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Radiografía de rodilla - 15/03/2023</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <img src="../img/Radiografia.PNG" class="img-fluid rounded mb-3">
                    </div>
                    <div class="col-md-6">
                        <h6>Diagnóstico:</h6>
                        <p>Fractura no desplazada del peroné...</p>

                        <h6 class="mt-3">Recomendaciones:</h6>
                        <ul>
                            <li>Inmovilización por 4 semanas</li>
                            <li>Control cada 7 días</li>
                        </ul>

                        <h6 class="mt-3">Doctor Responsable</h6>
                        <p>Dr. Luis Rodríguez</p>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button class="btn btn-primary">Descargar PDF</button>
            </div>

        </div>
    </div>
</div>

<footer class="footer mt-5 py-3">
    <div class="container text-center">
        <span class="text-muted">© 2025 MedWeb - Todos los derechos reservados</span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>


