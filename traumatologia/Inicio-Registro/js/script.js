// Funciones comunes
// ============================
// Función auxiliar para mostrar el formulario activo
// ============================
function showActiveForm() {
    const patientLogin = document.getElementById('patient-login-form');
    const patientRegister = document.getElementById('patient-register-form');
    const specialistLogin = document.getElementById('specialist-login-form');
    const specialistRegister = document.getElementById('specialist-register-form');
    const loginTab = document.getElementById('login-tab');
    const isPatient = document.getElementById('patient-type-btn')?.classList.contains('active');

    // Oculta todos los formularios
    [patientLogin, patientRegister, specialistLogin, specialistRegister].forEach(form => {
        form.classList.remove('active');
    });

    // Muestra el formulario correcto
    if (isPatient) {
        loginTab.classList.contains('active') ? patientLogin.classList.add('active') : patientRegister.classList.add('active');
    } else {
        loginTab.classList.contains('active') ? specialistLogin.classList.add('active') : specialistRegister.classList.add('active');
    }
}

// ============================
// Lógica para auth.html
// ============================
if (document.getElementById('auth-section')) {
    const patientBtn = document.getElementById('patient-type-btn');
    const specialistBtn = document.getElementById('specialist-type-btn');
    const loginTab = document.getElementById('login-tab');
    const registerTab = document.getElementById('register-tab');

    const setupTabSwitching = () => {
        patientBtn.addEventListener('click', () => {
            patientBtn.classList.add('active');
            specialistBtn.classList.remove('active');
            showActiveForm();
        });

        specialistBtn.addEventListener('click', () => {
            specialistBtn.classList.add('active');
            patientBtn.classList.remove('active');
            showActiveForm();
        });

        loginTab.addEventListener('click', () => {
            loginTab.classList.add('active');
            registerTab.classList.remove('active');
            showActiveForm();
        });

        registerTab.addEventListener('click', () => {
            registerTab.classList.add('active');
            loginTab.classList.remove('active');
            showActiveForm();
        });
    };

    const setupFormHandlers = () => {
        document.getElementById('patient-login-form').addEventListener('submit', e => {
            e.preventDefault();
            const email = document.getElementById('patient-email').value;
            console.log("Inicio de sesión del paciente:", email);
            window.location.href = '../Paciente/index.HTML';
        });

        document.getElementById('patient-register-form').addEventListener('submit', e => {
            e.preventDefault();
            const password = document.getElementById('new-patient-password').value;
            const confirmPassword = document.getElementById('new-patient-confirm-password').value;

            if (password !== confirmPassword) {
                alert("Las contraseñas no coinciden");
                return;
            }

            const name = document.getElementById('new-patient-name').value;
            const email = document.getElementById('new-patient-email').value;
            const phone = document.getElementById('new-patient-phone').value;
            console.log("Registro del paciente:", name, email, phone);
            window.location.href = '../Paciente/index.HTML';
        });

        document.getElementById('specialist-login-form').addEventListener('submit', e => {
            e.preventDefault();
            const email = document.getElementById('specialist-email').value;
            console.log("Inicio de sesión del especialista:", email);
            window.location.href = '../Doctor/index.html';
        });

        document.getElementById('specialist-register-form').addEventListener('submit', e => {
            e.preventDefault();
            const password = document.getElementById('new-specialist-password').value;
            const confirmPassword = document.getElementById('new-specialist-confirm-password').value;

            if (password !== confirmPassword) {
                alert("Las contraseñas no coinciden");
                return;
            }

            const name = document.getElementById('new-specialist-name').value;
            const email = document.getElementById('new-specialist-email').value;
            const license = document.getElementById('new-specialist-license').value;
            console.log("Registro del especialista:", name, email, license);
            window.location.href = '../Inicio-Registro/specialist-onboarding.html';
        });
    };

    const setupModals = () => {
        const contactModal = document.getElementById('contact-modal');
        const termsModal = document.getElementById('terms-modal');

        document.getElementById('contact-link').addEventListener('click', e => {
            e.preventDefault();
            contactModal.style.display = 'block';
        });

        document.getElementById('close-contact-modal').addEventListener('click', () => {
            contactModal.style.display = 'none';
        });

        document.getElementById('terms-link').addEventListener('click', e => {
            e.preventDefault();
            termsModal.style.display = 'block';
        });

        document.getElementById('close-modal').addEventListener('click', () => {
            termsModal.style.display = 'none';
        });

        document.getElementById('accept-terms').addEventListener('click', () => {
            termsModal.style.display = 'none';
        });

        window.addEventListener('click', event => {
            if (event.target === contactModal) contactModal.style.display = 'none';
            if (event.target === termsModal) termsModal.style.display = 'none';
        });
    };

    setupTabSwitching();
    setupFormHandlers();
    setupModals();
}

// ============================
// Lógica para specialist-onboarding.html
// ============================
if (document.getElementById('specialist-onboarding')) {
    const goToStep = (from, to, stepIndex) => {
        document.getElementById(`onboarding-step-${from}`).classList.remove('active');
        document.getElementById(`onboarding-step-${to}`).classList.add('active');

        const steps = document.querySelectorAll('.progress-step');
        steps[stepIndex - 1]?.classList.remove('active');
        steps[stepIndex - 1]?.classList.add('completed');
        steps[stepIndex]?.classList.add('active');
    };

    const backToStep = (from, to, stepIndex) => {
        document.getElementById(`onboarding-step-${from}`).classList.remove('active');
        document.getElementById(`onboarding-step-${to}`).classList.add('active');

        const steps = document.querySelectorAll('.progress-step');
        steps[stepIndex]?.classList.remove('active');
        steps[stepIndex - 1]?.classList.remove('completed');
        steps[stepIndex - 1]?.classList.add('active');
    };

    document.getElementById('next-step-1').addEventListener('click', () => goToStep(1, 2, 1));
    document.getElementById('next-step-2').addEventListener('click', () => goToStep(2, 3, 2));
    document.getElementById('next-step-3').addEventListener('click', () => goToStep(3, 4, 3));

    document.getElementById('prev-step-2').addEventListener('click', () => backToStep(2, 1, 1));
    document.getElementById('prev-step-3').addEventListener('click', () => backToStep(3, 2, 2));
    document.getElementById('prev-step-4').addEventListener('click', () => backToStep(4, 3, 3));

    document.getElementById('complete-onboarding').addEventListener('click', () => {
        window.location.href = '../Doctor/index.html';
    });
}
