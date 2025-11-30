<?php
session_start();
require "../conexion.php";

// Verificar si el usuario está logueado y es especialista
if (!isset($_SESSION["usuario_id"]) || $_SESSION["usuario_tipo"] !== "especialista") {
    header("Location: ../Inicio-Registro/auth.php");
    exit;
}

$doctor_id = $_SESSION["usuario_id"];

// Cuando se envía todo el onboarding
if (isset($_POST["finish_onboarding"])) {

    // Datos de todos los pasos
    $especialidades   = $_POST["especialidades"] ?? '';
    $universidad      = $_POST["universidad"] ?? '';
    $anio_graduacion  = $_POST["anio_graduacion"] ?? null;
    $experiencia      = $_POST["experiencia"] ?? null;

    $consultorio      = $_POST["consultorio"] ?? '';
    $direccion        = $_POST["direccion"] ?? '';
    $ciudad           = $_POST["ciudad"] ?? '';
    $cp               = $_POST["cp"] ?? '';

    $precio_consulta  = $_POST["precio_consulta"] ?? null;
    $acepta_seguros   = $_POST["acepta_seguros"] ?? '';
    $metodos_pago     = isset($_POST["metodos_pago"]) ? implode(", ", $_POST["metodos_pago"]) : '';

    // Guardar en BD
    $stmt = $conexion->prepare("
        UPDATE especialistas SET 
            especialidades=?, universidad=?, año_graduacion=?, experiencia=?,
            consultorio=?, direccion=?, ciudad=?, cp=?,
            precio_consulta=?, acepta_seguros=?, metodos_pago=?
        WHERE id=?
    ");

    $stmt->bind_param(
        "ssiiisssissi",
        $especialidades, $universidad, $anio_graduacion, $experiencia,
        $consultorio, $direccion, $ciudad, $cp,
        $precio_consulta, $acepta_seguros, $metodos_pago,
        $doctor_id
    );

    if ($stmt->execute()) {
        header("Location: index.html"); 
        exit;
    } else {
        $error = "Error al guardar onboarding.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MedWeb - Incorporación de Especialistas</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../Inicio-Registro/auth.php">
                <img src="../img/LOGO.jpg" alt="Medweb" height="60">
            </a>
        </div>
    </nav>

    <main>
        <section class="specialist-onboarding">
            <div class="onboarding-header">
                <h2>Completa tu perfil profesional</h2>
                <p>Necesitamos algunos datos adicionales para que los pacientes puedan encontrarte fácilmente</p>
            </div>

            <!-- Mensaje de error si algo falla -->
            <?php if (isset($error)): ?>
                <div style="color:red; font-weight:bold; margin-bottom:20px;">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <!-- TODO EL ONBOARDING envía aquí -->
            <form method="POST" id="onboarding-form">

                <!-- ------------------ PASO 1 ------------------ -->
                <div class="onboarding-step active" id="onboarding-step-1">
                    <h3>¿Cuál es tu especialidad?</h3>
                    <p>Selecciona hasta 3 especialidades</p>

                    <input type="hidden" name="especialidades" id="especialidades-input">

                    <div class="specialties-grid">
                        <div class="specialty-item">Medicina General</div>
                        <div class="specialty-item">Pediatría</div>
                        <div class="specialty-item">Ginecología</div>
                        <div class="specialty-item">Cardiología</div>
                        <div class="specialty-item">Dermatología</div>
                        <div class="specialty-item">Oftalmología</div>
                        <div class="specialty-item">Ortopedia</div>
                        <div class="specialty-item">Psiquiatría</div>
                        <div class="specialty-item">Neurología</div>
                    </div>

                    <div class="step-buttons">
                        <button type="button" class="btn btn-outline" onclick="showStep(2)">Omitir</button>
                        <button type="button" class="btn btn-secondary" onclick="showStep(2)">Continuar</button>
                    </div>
                </div>

                <!-- ------------------ PASO 2 ------------------ -->
                <div class="onboarding-step" id="onboarding-step-2">
                    <h3>Cuéntanos sobre tu formación académica</h3>

                    <div class="form-group">
                        <label>Universidad donde te graduaste</label>
                        <input type="text" name="universidad" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Año de graduación</label>
                        <input type="number" name="anio_graduacion" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Años de experiencia</label>
                        <input type="number" name="experiencia" class="form-control">
                    </div>

                    <div class="step-buttons">
                        <button type="button" class="btn btn-outline" onclick="showStep(1)">Anterior</button>
                        <button type="button" class="btn btn-secondary" onclick="showStep(3)">Continuar</button>
                    </div>
                </div>

                <!-- ------------------ PASO 3 ------------------ -->
                <div class="onboarding-step" id="onboarding-step-3">
                    <h3>¿Dónde atiendes a tus pacientes?</h3>

                    <div class="form-group">
                        <label>Nombre del consultorio/clínica</label>
                        <input type="text" name="consultorio" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Dirección</label>
                        <input type="text" name="direccion" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Ciudad</label>
                        <input type="text" name="ciudad" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Código Postal</label>
                        <input type="text" name="cp" class="form-control">
                    </div>

                    <div class="step-buttons">
                        <button type="button" class="btn btn-outline" onclick="showStep(2)">Anterior</button>
                        <button type="button" class="btn btn-secondary" onclick="showStep(4)">Continuar</button>
                    </div>
                </div>

                <!-- ------------------ PASO 4 ------------------ -->
                <div class="onboarding-step" id="onboarding-step-4">
                    <h3>Información sobre tus tarifas</h3>

                    <div class="form-group">
                        <label>Precio de la consulta (MXN)</label>
                        <input type="number" name="precio_consulta" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>¿Aceptas seguros médicos?</label>
                        <select name="acepta_seguros" class="form-control">
                            <option value="">Selecciona una opción</option>
                            <option value="Todos">Sí, todos los principales</option>
                            <option value="Algunos">Sí, algunos</option>
                            <option value="No">No por el momento</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Métodos de pago aceptados</label>

                        <div class="payment-methods">
                            <label><input type="checkbox" name="metodos_pago[]" value="Efectivo"> Efectivo</label>
                            <label><input type="checkbox" name="metodos_pago[]" value="Tarjeta"> Tarjeta</label>
                            <label><input type="checkbox" name="metodos_pago[]" value="Transferencia"> Transferencia</label>
                        </div>
                    </div>

                    <button type="submit" name="finish_onboarding" class="btn btn-secondary" style="width: 100%;">
                        Finalizar Registro
                    </button>
                </div>

            </form>
        </section>
    </main>


<script>
/* ---------------------- MANEJO DE ESPECIALIDADES ---------------------- */
let selectedSpecialties = [];
const maxSpecialties = 3;

document.querySelectorAll('.specialty-item').forEach(item => {
    item.addEventListener('click', function() {

        if (this.classList.contains('selected')) {
            this.classList.remove('selected');
            selectedSpecialties = selectedSpecialties.filter(s => s !== this.textContent);
        } 
        else if (selectedSpecialties.length < maxSpecialties) {
            this.classList.add('selected');
            selectedSpecialties.push(this.textContent);
        } 
        else {
            alert(`Solo puedes seleccionar hasta ${maxSpecialties} especialidades`);
        }

        document.getElementById("especialidades-input").value = selectedSpecialties.join(", ");
    });
});


/* ---------------------- MANEJO DE PASOS ---------------------- */
function showStep(stepNumber) {
    document.querySelectorAll('.onboarding-step').forEach(step => {
        step.classList.remove('active');
    });

    document.getElementById(`onboarding-step-${stepNumber}`).classList.add('active');
}
</script>

</body>
</html>
