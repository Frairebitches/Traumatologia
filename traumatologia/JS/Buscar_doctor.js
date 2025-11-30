document.addEventListener("DOMContentLoaded", function() {
    const inputBuscar = document.getElementById("buscarEspecialidad");
    const contenedor = document.getElementById("contenedorDoctores");

    fetch("../../controllers/obtener_medicos.php")
        .then(res => res.json())
        .then(data => {
            data.forEach(medico => {
                const div = document.createElement("div");
                div.className = "col-md-4 mb-4 doctor";
                div.dataset.especialidad = medico.especialidad;

                div.innerHTML = `
                    <div class="card text-center shadow">
                        <img src="../../assets/images/${medico.foto}" class="card-img-top" alt="Doctor">
                        <div class="card-body">
                            <h5 class="card-title">${medico.nombre}</h5>
                            <p class="card-text">Médico ${medico.especialidad}</p>
                            <p>⭐⭐⭐⭐⭐</p>
                            <a href="perfil_doctor.php?id=${medico.id}" class="btn btn-primary">Ver Perfil</a>
                        </div>
                    </div>
                `;
                contenedor.appendChild(div);
            });
        });

    inputBuscar.addEventListener("input", function() {
        const filtro = inputBuscar.value.toLowerCase();
        document.querySelectorAll(".doctor").forEach(doctor => {
            const esp = doctor.dataset.especialidad.toLowerCase();
            doctor.style.display = esp.includes(filtro) ? "block" : "none";
        });
    });
});