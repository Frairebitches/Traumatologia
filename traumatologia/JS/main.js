document.addEventListener('DOMContentLoaded', function() {
    // Validación de formularios
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
    
    // Mostrar/ocultar contraseña
    const togglePassword = document.querySelector('.toggle-password');
    if(togglePassword) {
        togglePassword.addEventListener('click', function() {
            const passwordInput = document.querySelector('#loginPassword');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    }
    
    // Simular envío de formulario de contacto
    const contactForm = document.getElementById('contactForm');
    if(contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Simular envío
            setTimeout(() => {
                const alert = document.createElement('div');
                alert.className = 'alert alert-success mt-3';
                alert.textContent = '¡Mensaje enviado con éxito! Nos pondremos en contacto contigo pronto.';
                contactForm.appendChild(alert);
                
                contactForm.reset();
                contactForm.classList.remove('was-validated');
                
                // Desplazarse al alert
                alert.scrollIntoView({ behavior: 'smooth' });
            }, 1000);
        });
    }
    
    // Tabla de análisis - acciones
    const analysisTable = document.querySelector('.table-analisis');
    if(analysisTable) {
        analysisTable.addEventListener('click', function(e) {
            if(e.target.classList.contains('btn-view')) {
                // Simular vista de análisis
                const analysisId = e.target.dataset.id;
                console.log('Ver análisis:', analysisId);
                
                // Aquí iría la lógica para mostrar el modal con los detalles
                const modal = new bootstrap.Modal(document.getElementById('analysisModal'));
                modal.show();
            }
        });
    }
    
    // Inicializar tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Funcionalidad común para los formularios de paciente
document.addEventListener('DOMContentLoaded', function() {
    // Validación de formularios
    const forms = document.querySelectorAll('#nuevoAnalisisForm, #nuevaCitaForm');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Validar campos requeridos
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                // Mostrar mensaje de error
                const errorAlert = document.createElement('div');
                errorAlert.className = 'alert alert-danger mt-3';
                errorAlert.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Por favor completa todos los campos requeridos';
                form.appendChild(errorAlert);
                
                // Desplazarse al primer error
                const firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalid.focus();
                }
            }
        });
    });
    
    // Resetear validación al cambiar campos
    document.querySelectorAll('input, textarea, select').forEach(input => {
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                this.classList.remove('is-invalid');
            }
        });
    });
    
    // Mostrar resumen de cita en el modal
    if (document.getElementById('nuevaCitaForm')) {
        document.getElementById('nuevaCitaForm').addEventListener('submit', function() {
            const tipo = document.getElementById('tipoCita').options[document.getElementById('tipoCita').selectedIndex].text;
            const doctor = document.getElementById('doctorCita').options[document.getElementById('doctorCita').selectedIndex].text;
            const fecha = document.getElementById('fechaCita').value;
            const hora = document.getElementById('horaCita').value;
            
            document.getElementById('resumenCita').textContent = `${tipo} con ${doctor} el ${fecha} a las ${hora}`;
        });
    }
});