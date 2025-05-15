document.addEventListener('DOMContentLoaded', function () {
    const verPerfilButton = document.getElementById('ver-perfil');
    const modalVerPerfil = document.getElementById('modal-ver-perfil');
    const closeButton = modalVerPerfil.querySelector('.close-button');

    // Abrir el modal al hacer clic en "Ver Perfil"
    verPerfilButton.addEventListener('click', function () {
        fetch('../Backend/perfil.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }

                // Mostrar los datos del perfil en el modal
                document.getElementById('perfil-nombre').textContent = data.nombre;
                document.getElementById('perfil-correo').textContent = data.email;
                document.getElementById('perfil-num-fotos').textContent = data.num_fotos;

                // Abrir el modal
                modalVerPerfil.style.display = 'block';
            })
            .catch(error => {
                console.error('Error al obtener el perfil:', error);
                alert('Hubo un problema al cargar el perfil.');
            });
    });
fetch('../Backend/gestion_rallys.php')
    .then(response => response.json())
    .then(data => {
        const select = document.getElementById('rally-select');
        select.innerHTML = '<option value="">Selecciona un rally</option>';
        if (Array.isArray(data)) {
            data.forEach(rally => {
                const option = document.createElement('option');
                option.value = rally.id_rally;
                option.textContent = `${rally.nombre} (finaliza: ${rally.fecha_fin})`;
                select.appendChild(option);
            });
        }
    });
    // Cerrar el modal al hacer clic en el botón de cerrar
    closeButton.addEventListener('click', function () {
        modalVerPerfil.style.display = 'none';
    });

    // Cerrar el modal al hacer clic fuera del contenido
    window.addEventListener('click', function (event) {
        if (event.target === modalVerPerfil) {
            modalVerPerfil.style.display = 'none';
        }
    });
});