<?php
session_start();
require "../conexion.php";

// Puedes descomentar estas dos líneas mientras pruebas si quieres ver errores de PHP
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// LOGIN PACIENTE
if (isset($_POST["patient_login"])) {
    $email = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';

    $stmt = $conexion->prepare("SELECT * FROM pacientes WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $paciente = $resultado->fetch_assoc();
        if (password_verify($password, $paciente["password"])) {
            $_SESSION["usuario_id"] = $paciente["id"];
            $_SESSION["usuario_tipo"] = "paciente";
            header("Location: ../Paciente/index.php");
            exit;
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Correo de paciente no encontrado.";
    }
}

// LOGIN ESPECIALISTA
if (isset($_POST["specialist_login"])) {
    $email = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';

    $stmt = $conexion->prepare("SELECT * FROM especialistas WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $doc = $resultado->fetch_assoc();
        if (password_verify($password, $doc["password"])) {
            $_SESSION["usuario_id"] = $doc["id"];
            $_SESSION["usuario_tipo"] = "especialista";
            header("Location: ../Doctor/index.html");
            exit;
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Correo de especialista no encontrado.";
    }
}

// REGISTRO PACIENTE
if (isset($_POST["patient_register"])) {
    $nombre   = $_POST["nombre"]   ?? '';
    $email    = $_POST["email"]    ?? '';
    $telefono = $_POST["telefono"] ?? '';
    $pass     = $_POST["password"] ?? '';
    $conf     = $_POST["confirm_password"] ?? '';

    if ($pass !== $conf) {
        $error = "Las contraseñas del paciente no coinciden.";
    } else {
        $password = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $conexion->prepare(
            "INSERT INTO pacientes (nombre, email, telefono, password) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $nombre, $email, $telefono, $password);
        if ($stmt->execute()) {
            $success = "Paciente registrado correctamente. Ahora puedes iniciar sesión.";
        } else {
            $error = "Error al registrar paciente (correo quizá ya registrado).";
        }
    }
}

// REGISTRO ESPECIALISTA
if (isset($_POST["specialist_register"])) {
    $nombre = $_POST["nombre"] ?? '';
    $email  = $_POST["email"]  ?? '';
    $cedula = $_POST["cedula"] ?? '';
    $pass   = $_POST["password"] ?? '';
    $conf   = $_POST["confirm_password"] ?? '';

    if ($pass !== $conf) {
        $error = "Las contraseñas del especialista no coinciden.";
    } else {
        $password = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $conexion->prepare(
            "INSERT INTO especialistas (nombre, email, cedula_profesional, password) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $nombre, $email, $cedula, $password);

        if ($stmt->execute()) {
            // ⬇️ NUEVO: guardamos sesión y mandamos al onboarding
            $nuevo_id = $stmt->insert_id;
            $_SESSION["usuario_id"] = $nuevo_id;
            $_SESSION["usuario_tipo"] = "especialista";

            // specialist-onboarding.php está en la MISMA carpeta que auth.php
            header("Location: specialist-onboarding.php");
            exit;
        } else {
            $error = "Error al registrar especialista (correo o cédula quizá ya registrados).";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedWeb - Autenticación</title>
    <link rel="stylesheet" href="../Inicio-Registro/css/styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../Inicio-Registro/auth.php">
                <img src="../img/LOGO.jpg" alt="Medweb" height="60">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../Pagina/index.HTML">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../Pagina/Especialistas.html">Especialistas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="nav-login-btn">Iniciar Sesión</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="nav-register-btn">Registrarse</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <section class="auth-container" id="auth-section">
            <div class="auth-image">
                <img src="../img/Tusalud.png" alt="Tu salud merece al mejor especialista - MedWeb" class="auth-main-image">
            </div>
            <div class="auth-form-container">
                <div class="auth-header">
                    <h2>Bienvenido a MedWeb</h2>
                    <p>Inicia sesión o regístrate para continuar</p>
                </div>
                
                <!-- Mensajes PHP -->
                <?php if (isset($error)): ?>
                    <div style="color:#f44336; margin-bottom:15px; font-weight:bold;">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($success)): ?>
                    <div style="color:#4caf50; margin-bottom:15px; font-weight:bold;">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>
                
                <div class="user-type-selector">
                    <button class="user-type-btn active" id="patient-type-btn">Paciente</button>
                    <button class="user-type-btn" id="specialist-type-btn">Especialista</button>
                </div>
                
                <div class="auth-tabs">
                    <div class="auth-tab active" id="login-tab">Iniciar Sesión</div>
                    <div class="auth-tab" id="register-tab">Registrarse</div>
                </div>
                
                <!-- LOGIN PACIENTE -->
                <form class="auth-form active" id="patient-login-form" method="POST">
                    <input type="hidden" name="patient_login" value="1">
                    <div class="form-group">
                        <label for="patient-email">Correo electrónico</label>
                        <input type="email" id="patient-email" name="email" class="form-control" placeholder="correo@ejemplo.com" required>
                    </div>
                    <div class="form-group">
                        <label for="patient-password">Contraseña</label>
                        <input type="password" id="patient-password" name="password" class="form-control" placeholder="Contraseña" required>
                    </div>
                    <div class="forgot-password">
                        <a href="#" id="forgot-password-link">¿Olvidaste tu contraseña?</a>
                        <div id="password-reset-modal" style="display: none; position: fixed; z-index: 999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);"></div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Iniciar Sesión</button>
                </form>
                
                <!-- REGISTRO PACIENTE -->
                <form class="auth-form" id="patient-register-form" method="POST">
                    <input type="hidden" name="patient_register" value="1">
                    <div class="form-group">
                        <label for="new-patient-name">Nombre completo</label>
                        <input type="text" id="new-pient-name" name="nombre" class="form-control" placeholder="Nombre y apellidos" required>
                    </div>
                    <div class="form-group">
                        <label for="new-patient-email">Correo electrónico</label>
                        <input type="email" id="new-patient-email" name="email" class="form-control" placeholder="correo@ejemplo.com" required>
                    </div>
                    <div class="form-group">
                        <label for="new-patient-phone">Teléfono</label>
                        <input type="tel" id="new-patient-phone" name="telefono" class="form-control" placeholder="(123) 456-7890">
                    </div>
                    <div class="form-group">
                        <label for="new-patient-password">Contraseña</label>
                        <input type="password" id="new-patient-password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" required>
                    </div>
                    <div class="form-group">
                        <label for="new-patient-confirm-password">Confirmar contraseña</label>
                        <input type="password" id="new-patient-confirm-password" name="confirm_password" class="form-control" placeholder="Confirma tu contraseña" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Crear cuenta</button>
                </form>
                
                <!-- LOGIN ESPECIALISTA -->
                <form class="auth-form" id="specialist-login-form" method="POST">
                    <input type="hidden" name="specialist_login" value="1">
                    <div class="form-group">
                        <label for="specialist-email">Correo electrónico profesional</label>
                        <input type="email" id="specialist-email" name="email" class="form-control" placeholder="doc.nombre@hospital.com" required>
                    </div>
                    <div class="form-group">
                        <label for="specialist-password">Contraseña</label>
                        <input type="password" id="specialist-password" name="password" class="form-control" placeholder="Contraseña" required>
                    </div>
                    <div class="forgot-password">
                        <a href="#">¿Olvidaste tu contraseña?</a>
                    </div>
                    <button type="submit" class="btn btn-secondary" style="width: 100%;">Iniciar Sesión como Especialista</button>
                    <div class="form-footer">
                        <p>¿Tienes dificultades para acceder? <a href="#" id="contact-link">Contáctanos</a></p>
                        <div id="contact-modal" style="display: none; position: fixed; z-index: 999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
                            <div style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 500px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                                <span style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;" id="close-contact-modal">×</span>
                                <h3 style="color: var(--primary-color); margin-bottom: 20px;">Información de Contacto</h3>
                                <p style="margin-bottom: 12px;"><strong>Correo electrónico:</strong> soportemedweb@gmail.com</p>
                                <p style="margin-bottom: 12px;"><strong>Llámanos:</strong> 8120293847</p>
                                <p style="margin-bottom: 12px;"><strong>Mándanos mensaje:</strong> 810293847</p>
                            </div>
                        </div>
                    </div>
                </form>
                
                <!-- REGISTRO ESPECIALISTA -->
                <form class="auth-form" id="specialist-register-form" method="POST">
                    <input type="hidden" name="specialist_register" value="1">
                    <div class="form-group">
                        <label for="new-specialist-name">Nombre completo</label>
                        <input type="text" id="new-specialist-name" name="nombre" class="form-control" placeholder="Dr./Dra. Nombre y apellidos" required>
                    </div>
                    <div class="form-group">
                        <label for="new-specialist-email">Correo electrónico profesional</label>
                        <input type="email" id="new-specialist-email" name="email" class="form-control" placeholder="doc.nombre@hospital.com" required>
                    </div>
                    <div class="form-group">
                        <label for="new-specialist-license">Número de cédula profesional</label>
                        <input type="text" id="new-specialist-license" name="cedula" class="form-control" placeholder="XXXXX" required>
                    </div>
                    <div class="form-group">
                        <label for="new-specialist-password">Contraseña</label>
                        <input type="password" id="new-specialist-password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" required>
                    </div>
                    <div class="form-group">
                        <label for="new-specialist-confirm-password">Confirmar contraseña</label>
                        <input type="password" id="new-specialist-confirm-password" name="confirm_password" class="form-control" placeholder="Confirma tu contraseña" required>
                    </div>
                    <button type="submit"  class="btn btn-secondary" style="width: 100%;">Crear cuenta como Especialista</button>
                    <div class="form-footer">
                        <p>Al registrarte aceptas nuestros <a href="#" id="terms-link">Términos y Condiciones</a></p>
                        <div id="terms-modal" style="display: none; position: fixed; z-index: 999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
                            <div style="background-color: #fefefe; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 700px; max-height: 80vh; overflow-y: auto; border-radius: 8px;">
                                <span style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;" id="close-modal">×</span>
                                <h2 style="color: var(--primary-color); margin-bottom: 20px;">Términos y Condiciones de MedWeb</h2>
                                <p style="margin-bottom: 15px;">Le damos la bienvenida a MedWeb, una plataforma diseñada para facilitar la conexión entre pacientes y especialistas en diversas áreas de la salud. Al registrarse y utilizar nuestros servicios, usted acepta los siguientes términos y condiciones en su totalidad:</p>
                                <h3 style="margin: 20px 0 10px; color: var(--dark-gray);">1. Uso de la Plataforma</h3>
                                <p style="margin-bottom: 15px;">MedWeb opera como una plataforma que facilita la búsqueda y el contacto entre profesionales de la salud y pacientes. Los especialistas pueden registrarse para ofrecer información sobre sus servicios. Se aclara que MedWeb no reemplaza el criterio médico profesional ni garantiza resultados específicos derivados de los tratamientos ofrecidos por los especialistas.</p>
                                <h3 style="margin: 20px 0 10px; color: var(--dark-gray);">2. Registro de Usuarios</h3>
                                <p style="margin-bottom: 8px;"><strong>Pacientes:</strong> Los pacientes pueden crear una cuenta para contactar a especialistas y solicitar citas.</p>
                                <p style="margin-bottom: 8px;"><strong>Especialistas:</strong> Los especialistas deben proporcionar información precisa y actualizada sobre sus credenciales, experiencia profesional y áreas de especialización.</p>
                                <p style="margin-bottom: 15px;"><strong>Verificación:</strong> MedWeb se reserva el derecho de verificar la información de los perfiles y de rechazar o eliminar registros que se consideren fraudulentos o que incumplan estos términos.</p>
                                <h3 style="margin: 20px 0 10px; color: var(--dark-gray);">3. Limitación de Responsabilidad de MedWeb</h3>
                                <p style="margin-bottom: 15px;">MedWeb actúa únicamente como un intermediario tecnológico entre usuarios y especialistas. Por lo tanto, MedWeb no asume responsabilidad alguna por la calidad de los servicios médicos prestados por los especialistas, las tarifas acordadas directamente entre las partes, ni por cualquier acuerdo o interacción que se establezca fuera de la plataforma.</p>
                                <h3 style="margin: 20px 0 10px; color: var(--dark-gray);">4. Protección de Datos Personales</h3>
                                <p style="margin-bottom: 15px;">MedWeb recopila y almacena datos personales de conformidad con las leyes y regulaciones de privacidad aplicables. La información proporcionada por los usuarios será tratada con la debida confidencialidad y no será compartida con terceros sin el consentimiento explícito del usuario, salvo que sea requerido por ley. Para más detalles, consulte nuestra Política de Privacidad.</p>
                                <h3 style="margin: 20px 0 10px; color: var(--dark-gray);">5. Pagos y Tarifas</h3>
                                <p style="margin-bottom: 15px;">En caso de que MedWeb facilite el procesamiento de pagos a través de la plataforma, las tarifas de servicio aplicables y las condiciones de pago serán detalladas de manera específica en la sección correspondiente de la plataforma.</p>
                                <h3 style="margin: 20px 0 10px; color: var(--dark-gray);">6. Modificaciones de los Términos y Condiciones</h3>
                                <p style="margin-bottom: 15px;">MedWeb se reserva el derecho de modificar estos términos y condiciones en cualquier momento que lo considere necesario. Se recomienda a los usuarios revisar periódicamente esta página para estar al tanto de cualquier actualización. La continuidad en el uso de la plataforma después de la publicación de modificaciones constituirá la aceptación de los nuevos términos.</p>
                                <h3 style="margin: 20px 0 10px; color: var(--dark-gray);">7. Contacto</h3>
                                <p style="margin-bottom: 15px;">Si tiene alguna pregunta o necesita asistencia, puede comunicarse con nuestro equipo de soporte a través de support@medweb.com.</p>
                                <div style="text-align: center; margin-top: 30px;">
                                    <button class="btn btn-primary" id="accept-terms">Aceptar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <!-- IMPORTANTE: ya no cargamos script.js externo -->
    <script src="../Inicio-Registro/js/script.js"></script>

    <script>
        function showActiveForm() {
            const isPatient = document.getElementById('patient-type-btn').classList.contains('active');
            const isLogin = document.getElementById('login-tab').classList.contains('active');
            
            document.querySelectorAll('.auth-form').forEach(form => {
                form.classList.remove('active');
            });
            
            if (isPatient && isLogin) {
                document.getElementById('patient-login-form').classList.add('active');
            } else if (isPatient && !isLogin) {
                document.getElementById('patient-register-form').classList.add('active');
            } else if (!isPatient && isLogin) {
                document.getElementById('specialist-login-form').classList.add('active');
            } else {
                document.getElementById('specialist-register-form').classList.add('active');
            }
        }

        document.getElementById('nav-login-btn').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('login-tab').classList.add('active');
            document.getElementById('register-tab').classList.remove('active');
            showActiveForm();
            document.getElementById('auth-section').scrollIntoView({ behavior: 'smooth' });
        });

        document.getElementById('nav-register-btn').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('register-tab').classList.add('active');
            document.getElementById('login-tab').classList.remove('active');
            showActiveForm();
            document.getElementById('auth-section').scrollIntoView({ behavior: 'smooth' });
        });

        document.getElementById('login-tab').addEventListener('click', function() {
            this.classList.add('active');
            document.getElementById('register-tab').classList.remove('active');
            showActiveForm();
        });

        document.getElementById('register-tab').addEventListener('click', function() {
            this.classList.add('active');
            document.getElementById('login-tab').classList.remove('active');
            showActiveForm();
        });

        document.getElementById('patient-type-btn').addEventListener('click', function() {
            this.classList.add('active');
            document.getElementById('specialist-type-btn').classList.remove('active');
            showActiveForm();
        });

        document.getElementById('specialist-type-btn').addEventListener('click', function() {
            this.classList.add('active');
            document.getElementById('patient-type-btn').classList.remove('active');
            showActiveForm();
        });

        const urlParams = new URLSearchParams(window.location.search);
        const userType = urlParams.get('type');
        const tab = urlParams.get('tab');

        if (userType === 'specialist') {
            document.getElementById('specialist-type-btn').classList.add('active');
            document.getElementById('patient-type-btn').classList.remove('active');
        }
        if (tab === 'register') {
            document.getElementById('register-tab').classList.add('active');
            document.getElementById('login-tab').classList.remove('active');
        }
        showActiveForm();

        // Modales contacto y términos
        const contactLink = document.getElementById('contact-link');
        const contactModal = document.getElementById('contact-modal');
        const closeContactModal = document.getElementById('close-contact-modal');
        if (contactLink && contactModal && closeContactModal) {
            contactLink.addEventListener('click', function(e) {
                e.preventDefault();
                contactModal.style.display = 'block';
            });
            closeContactModal.addEventListener('click', function() {
                contactModal.style.display = 'none';
            });
        }

        const termsLink = document.getElementById('terms-link');
        const termsModal = document.getElementById('terms-modal');
        const closeModal = document.getElementById('close-modal');
        const acceptTerms = document.getElementById('accept-terms');
        if (termsLink && termsModal && closeModal && acceptTerms) {
            termsLink.addEventListener('click', function(e) {
                e.preventDefault();
                termsModal.style.display = 'block';
            });
            closeModal.addEventListener('click', function() {
                termsModal.style.display = 'none';
            });
            acceptTerms.addEventListener('click', function(e) {
                e.preventDefault();
                termsModal.style.display = 'none';
            });
        }

        window.addEventListener('click', function(event) {
            if (contactModal && event.target === contactModal) {
                contactModal.style.display = 'none';
            }
            if (termsModal && event.target === termsModal) {
                termsModal.style.display = 'none';
            }
        });
    </script>
</body>
</html>
