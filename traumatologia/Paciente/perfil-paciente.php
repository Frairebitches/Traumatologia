<?php
session_start();
require "../conexion.php";

if (!isset($_SESSION["usuario_id"]) || $_SESSION["usuario_tipo"] !== "paciente") {
    header("Location: ../Inicio-Registro/auth.php");
    exit;
}

$paciente_id = $_SESSION["usuario_id"];

// Obtener datos actuales
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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["guardar_perfil"])) {

    $nombre   = trim($_POST["nombre"] ?? '');
    $edad     = intval($_POST["edad"] ?? 0);
    $email    = trim($_POST["email"] ?? '');
    $telefono = trim($_POST["telefono"] ?? '');
    $alergias = trim($_POST["alergias"] ?? '');

    // Foto
    $foto_perfil = $paciente["foto_perfil"];

    if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] === UPLOAD_ERR_OK) {
        $tmp  = $_FILES["foto"]["tmp_name"];
        $name = basename($_FILES["foto"]["name"]);
        $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        $permitidas = ["jpg","jpeg","png","gif"];
        if (in_array($ext, $permitidas)) {
            $dir = "../img/pacientes/";
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            $nuevoNombre = "paciente_" . $paciente_id . "_" . time() . "." . $ext;
            $rutaFinal   = $dir . $nuevoNombre;
            if (move_uploaded_file($tmp, $rutaFinal)) {
                $foto_perfil = $rutaFinal;
            }
        }
    }

    $stmtUpdate = $conexion->prepare("
        UPDATE pacientes 
        SET nombre = ?, edad = ?, email = ?, telefono = ?, alergias = ?, foto_perfil = ?
        WHERE id = ?
    ");
    $stmtUpdate->bind_param("sissssi", $nombre, $edad, $email, $telefono, $alergias, $foto_perfil, $paciente_id);

    if ($stmtUpdate->execute()) {
        $mensaje_ok = "Perfil actualizado correctamente.";
        // refrescamos datos
        $paciente["nombre"]       = $nombre;
        $paciente["edad"]         = $edad;
        $paciente["email"]        = $email;
        $paciente["telefono"]     = $telefono;
        $paciente["alergias"]     = $alergias;
        $paciente["foto_perfil"]  = $foto_perfil;
    } else {
        $mensaje_error = "Error al actualizar el perfil.";
    }
}

$nombre   = $paciente["nombre"] ?? "";
$edad     = $paciente["edad"] ?? "";
$email    = $paciente["email"] ?? "";
$telefono = $paciente["telefono"] ?? "";
$alergias = $paciente["alergias"] ?? "";
$foto     = $paciente["foto_perfil"] ?: "https://via.placeholder.com/150";
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mi Perfil</title>
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
    .profile-img {
      width: 150px;
      height: 150px;
      object-fit: cover;
      border-radius: 50%;
      border: 4px solid #0d6efd;
    }
    input[type="file"] {
      display: none;
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
                      <a class="nav-link" href="Analisis.HTML">
                          <i class="bi bi-file-medical me-1"></i>Mis Análisis
                      </a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="Citas.HTML">
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

  <!-- Perfil -->
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-10">
        <div class="card p-4">
          <h4 class="mb-4 text-primary text-center"><i class="bi bi-person-circle me-2"></i>Perfil del Paciente</h4>

          <?php if ($mensaje_ok): ?>
            <div class="alert alert-success"><?= htmlspecialchars($mensaje_ok) ?></div>
          <?php endif; ?>
          <?php if ($mensaje_error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($mensaje_error) ?></div>
          <?php endif; ?>

          <form method="POST" enctype="multipart/form-data">
          <div class="row align-items-center">
            <!-- Imagen -->
            <div class="col-md-4 text-center mb-3 mb-md-0">
              <img id="imagenPerfil" src="<?= htmlspecialchars($foto) ?>" class="profile-img mb-3" alt="Foto de perfil">
              <br>
              <label class="btn btn-outline-primary btn-sm" for="archivoImagen">
                <i class="bi bi-upload me-1"></i>Cambiar foto
              </label>
              <input type="file" id="archivoImagen" name="foto" accept="image/*" onchange="mostrarVistaPrevia(event)">
            </div>
            
            <!-- Información -->
            <div class="col-md-8">
              <div class="mb-3">
                <label class="form-label"><strong>Nombre:</strong></label>
                <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($nombre) ?>">
              </div>
              <div class="mb-3">
                <label class="form-label"><strong>Edad</strong></label>
                <input type="number" class="form-control" name="edad" value="<?= htmlspecialchars($edad) ?>">
              </div>
              <div class="mb-3">
                <label class="form-label"><strong>Email:</strong></label>
                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($email) ?>">
              </div>
              <div class="mb-3">
                <label class="form-label"><strong>Teléfono:</strong></label>
                <input type="tel" class="form-control" name="telefono" value="<?= htmlspecialchars($telefono) ?>">
              </div>
              <div class="mb-3">
                <label class="form-label"><strong>Alergias:</strong></label>
                <input type="text" class="form-control" name="alergias" value="<?= htmlspecialchars($alergias) ?>">
              </div>
              <button type="submit" name="guardar_perfil" class="btn btn-primary w-100 mt-2">Guardar Cambios</button>
            </div>
          </div>
          </form>

        </div>
      </div>
    </div>
  </div>

  <script>
    function mostrarVistaPrevia(event) {
      const archivo = event.target.files[0];
      if (archivo) {
        const lector = new FileReader();
        lector.onload = function(e) {
          document.getElementById('imagenPerfil').src = e.target.result;
        };
        lector.readAsDataURL(archivo);
      }
    }

    function cerrarSesion() {
      window.location.href = "../Inicio-Registro/auth.php"; 
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
