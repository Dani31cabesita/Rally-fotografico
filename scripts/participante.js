//Lógica del panel de participante (ver perfil, cargar rallys activos y gestionar modal de perfil)

document.addEventListener('DOMContentLoaded', function () {
    // Elementos principales
    const verPerfilButton = document.getElementById('ver-perfil');
    const modalVerPerfil = document.getElementById('modal-ver-perfil');
    const closeButton = modalVerPerfil.querySelector('.close-button');

    // Al hacer clic en "Ver Perfil", solicita los datos al backend y muestra el modal
    verPerfilButton.addEventListener('click', function () {
        fetch('../Backend/perfil.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                // Rellena los campos del modal con los datos del perfil
                document.getElementById('perfil-nombre').textContent = data.nombre;
                document.getElementById('perfil-correo').textContent = data.email;
                document.getElementById('perfil-num-fotos').textContent = data.num_fotos;
                modalVerPerfil.style.display = 'block';
            })
            .catch(error => {
                console.error('Error al obtener el perfil:', error);
                alert('Hubo un problema al cargar el perfil.');
            });
    });

    // Carga los rallys activos y los añade al select del formulario de subida de imagen
    fetch('../Backend/gestion_rallys.php')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('rally-select');
            select.innerHTML = '<option value="">Selecciona un rally</option>';
            if (Array.isArray(data) && data.length > 0) {
                // Añade cada rally como opción
                data.forEach(rally => {
                    const option = document.createElement('option');
                    option.value = rally.id_rally;
                    option.textContent = `${rally.nombre} (Del ${rally.fecha_inicio} al ${rally.fecha_fin})`;
                    select.appendChild(option);
                });
            } else {
                // Si no hay rallys activos, deshabilita el select
                select.innerHTML = '<option value="">No hay rallys activos disponibles</option>';
                select.disabled = true;
            }
        })
        .catch(() => {
            // Si hay error al cargar los rallys, muestra mensaje y deshabilita el select
            const select = document.getElementById('rally-select');
            select.innerHTML = '<option value="">Error al cargar los rallys</option>';
            select.disabled = true;
        });

    // Cierra el modal al hacer clic en la X
    closeButton.addEventListener('click', function () {
        modalVerPerfil.style.display = 'none';
    });

    // Cierra el modal al hacer clic fuera del contenido del modal
    window.addEventListener('click', function (event) {
        if (event.target === modalVerPerfil) {
            modalVerPerfil.style.display = 'none';
        }
    });
});