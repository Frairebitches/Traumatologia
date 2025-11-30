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

// Datos para mostrar
$nombre          = $paciente["nombre"] ?? "Paciente";
$correo          = $paciente["email"] ?? "";
$telefono        = $paciente["telefono"] ?? "";
$edad            = $paciente["edad"] ?? null;
$tipo_sanguineo  = $paciente["tipo_sanguineo"] ?? null;
$alergias        = $paciente["alergias"] ?? null;
$foto_perfil     = $paciente["foto_perfil"] ?? "";
$codigo          = $paciente["paciente_codigo"] ?? "";

// Si no hay código en BD, generamos uno “virtual” con el id
if (!$codigo) {
    $codigo = "PAC-" . date("Y") . "-" . str_pad($paciente_id, 3, "0", STR_PAD_LEFT);
}

// Foto por defecto
if (!$foto_perfil) {
    $foto_perfil = "../img/HenryCavil.jpg"; // pon aquí tu imagen default
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Médico - Mi Perfil</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary-blue: #2563eb;
            --light-blue: #3b82f6;
            --pale-blue: #dbeafe;
            --accent-blue: #1e8baf;
            --soft-white: #f8fafc;
            --pure-white: #ffffff;
            --text-gray: #64748b;
            --border-gray: #e2e8f0;
        }

        body {
            background: linear-gradient(135deg, var(--pale-blue) 0%, var(--soft-white) 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .main-container {
            padding: 3rem 1rem;
        }

        .profile-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.1);
            transition: all 0.3s ease;
            background: var(--pure-white);
            overflow: hidden;
        }

        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(37, 99, 235, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-blue), var(--light-blue)) !important;
            border: none;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(30px, -30px);
        }

        .card-header h3 {
            font-weight: 600;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .profile-image {
            width: 120px;
            height: 120px;
            border: 4px solid var(--primary-blue);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.2);
            transition: all 0.3s ease;
        }

        .profile-image:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 30px rgba(37, 99, 235, 0.3);
        }

        .patient-name {
            color: var(--primary-blue);
            font-weight: 700;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
        }

        .patient-id {
            color: var(--text-gray);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .info-list {
            border: none;
            margin-top: 1.5rem;
        }

        .info-list .list-group-item {
            border: none;
            background: var(--soft-white);
            margin-bottom: 0.5rem;
            border-radius: 10px;
            padding: 1rem;
            transition: all 0.3s ease;
        }

        .info-list .list-group-item:hover {
            background: var(--pale-blue);
            transform: translateX(5px);
        }

        .info-list .list-group-item strong {
            color: var(--primary-blue);
        }

        .btn-modern {
            background: linear-gradient(135deg, var(--primary-blue), var(--light-blue));
            border: none;
            border-radius: 25px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.2);
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
            background: linear-gradient(135deg, var(--accent-blue), var(--primary-blue));
        }

        .analysis-table {
            background: var(--pure-white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(37, 99, 235, 0.08);
        }

        .table thead th {
            background: linear-gradient(135deg, var(--pale-blue), rgba(219, 234, 254, 0.5));
            color: var(--primary-blue);
            font-weight: 600;
            border: none;
            padding: 1.2rem 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: var(--pale-blue);
            transform: scale(1.01);
        }

        .table tbody td {
            padding: 1.2rem 1rem;
            border: none;
            vertical-align: middle;
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge.bg-success {
            background: linear-gradient(135deg, #10b981, #059669) !important;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2);
        }

        .btn-outline-primary {
            color: var(--primary-blue);
            border-color: var(--primary-blue);
            border-radius: 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: var(--primary-blue);
            border-color: var(--primary-blue);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
        }

        .btn-outline-secondary {
            color: var(--text-gray);
            border-color: var(--border-gray);
            border-radius: 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            background: var(--text-gray);
            border-color: var(--text-gray);
            transform: translateY(-2px);
        }

        .pagination {
            margin: 0;
        }

        .page-link {
            color: var(--primary-blue);
            border: none;
            border-radius: 10px;
            margin: 0 0.2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .page-link:hover {
            background: var(--primary-blue);
            color: white;
            transform: translateY(-2px);
        }

        .page-item.active .page-link {
            background: linear-gradient(135deg, var(--primary-blue), var(--light-blue));
            border: none;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        .icon-accent {
            color: var(--primary-blue);
            margin-right: 0.5rem;
        }

        .floating-elements {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
        }

        .floating-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(37, 99, 235, 0.05);
            animation: float 6s ease-in-out infinite;
        }

        .floating-circle:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-circle:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .floating-circle:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 2rem 1rem;
            }
            
            .profile-card {
                margin-bottom: 2rem;
            }
            
            .btn-modern {
                padding: 0.6rem 1.5rem;
                font-size: 0.9rem;
            }
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
                        <a class="nav-link active" href="index.php">
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

    <div class="container-fluid main-container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card profile-card">
                    <div class="card-header text-white">
                        <h3 class="h5 mb-0">
                            <i class="fas fa-user-circle icon-accent" style="color: white;"></i>
                            Mi Perfil
                        </h3>
                    </div>
                    <div class="card-body text-center">
                        <img src="<?= htmlspecialchars($foto_perfil) ?>" 
                             class="rounded-circle img-fluid mb-3 profile-image" 
                             width="150" height="150" alt="Paciente">
                        <h4 class="patient-name"><?= htmlspecialchars($nombre) ?></h4>
                        <p class="patient-id">
                            <i class="fas fa-id-card icon-accent"></i>
                            ID: <?= htmlspecialchars($codigo) ?>
                        </p>
                        
                        <ul class="list-group info-list">
                            <li class="list-group-item">
                                <i class="fas fa-birthday-cake icon-accent"></i>
                                <strong>Edad:</strong> <?= $edad ? intval($edad) . " años" : "Sin registrar" ?>
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-tint icon-accent"></i>
                                <strong>Tipo sanguíneo:</strong> <?= $tipo_sanguineo ? htmlspecialchars($tipo_sanguineo) : "Sin registrar" ?>
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-exclamation-triangle icon-accent"></i>
                                <strong>Alergias:</strong> <?= $alergias ? htmlspecialchars($alergias) : "Ninguna registrada" ?>
                            </li>
                        </ul>
                        
                        <a href="perfil-paciente.php" class="btn btn-modern text-white mt-3">
                            <i class="fas fa-edit"></i>
                            Editar perfil
                        </a>

                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card profile-card">
                    <div class="card-header text-white">
                        <h3 class="h5 mb-0">
                            <i class="fas fa-file-medical icon-accent" style="color: white;"></i>
                            Mis Análisis
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive analysis-table">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-calendar-alt icon-accent"></i>Fecha</th>
                                        <th><i class="fas fa-stethoscope icon-accent"></i>Tipo</th>
                                        <th><i class="fas fa-user-md icon-accent"></i>Doctor</th>
                                        <th><i class="fas fa-check-circle icon-accent"></i>Estado</th>
                                        <th><i class="fas fa-cogs icon-accent"></i>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>15/03/2023</strong></td>
                                        <td>Radiografía de rodilla</td>
                                        <td>Dr. Rodríguez</td>
                                        <td><span class="badge bg-success">Completado</span></td>
                                        <td>
                                            <a href="../Paciente/Analisis.HTML" class="btn btn-sm btn-outline-primary me-1">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                            <button class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-download"></i> Descargar
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>08/03/2023</strong></td>
                                        <td>Análisis de sangre</td>
                                        <td>Dra. García</td>
                                        <td><span class="badge bg-success">Completado</span></td>
                                        <td>
                                            <a href="../Paciente/Analisis.HTML" class="btn btn-sm btn-outline-primary me-1">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                            <button class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-download"></i> Descargar
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>25/02/2023</strong></td>
                                        <td>Electrocardiograma</td>
                                        <td>Dr. Martínez</td>
                                        <td><span class="badge bg-success">Completado</span></td>
                                        <td>
                                            <a href="../Paciente/Analisis.HTML" class="btn btn-sm btn-outline-primary me-1">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                            <button class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-download"></i> Descargar
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="../Pagina/Especialistas.html" class="btn btn-modern text-white">
                                <i class="fas fa-plus"></i>
                                Nueva consulta
                            </a>
                            <nav>
                                <ul class="pagination pagination-sm">
                                    <li class="page-item disabled">
                                        <a class="page-link" href="#" tabindex="-1">
                                            <i class="fas fa-chevron-left"></i> Anterior
                                        </a>
                                    </li>
                                    <li class="page-item active">
                                        <a class="page-link" href="#">1</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">2</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">3</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">
                                            Siguiente <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function cerrarSesion() {
            window.location.href = "../Inicio-Registro/auth.php"; 
        }
        // Añadir efectos de hover interactivos
        document.querySelectorAll('.table tbody tr').forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.02)';
                this.style.boxShadow = '0 8px 25px rgba(37, 
                99, 235, 0.1)';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
                this.style.boxShadow = 'none';
            });

        // Efecto de pulse en botones
        document.querySelectorAll('.btn-modern').forEach(btn => {
            btn.addEventListener('click', function() {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 100);
            });
        });
        
        function cerrarSesion() {
      alert("Sesión cerrada.");
      window.location.href = "../Inicio-Registro/auth.html"; 
    }
    </script>
</body>
</html>