//Gestiona la visualización, creación y eliminación de usuarios para admin y muestra el perfil para participantes

document.addEventListener('DOMContentLoaded', function () {
    // Elementos principales de la página de gestión de usuarios
    const usuariosContainer = document.getElementById('usuarios-container');
    const crearUsuarioContainer = document.getElementById('crear-usuario-container');
    const crearUsuarioForm = document.getElementById('crear-usuario-form');

    // Carga y muestra la lista de usuarios (admin) o los datos del participante
    if (usuariosContainer) {
        fetch('../Backend/gestion_usuarios.php')
            .then(res => res.json())
            .then(data => {
                usuariosContainer.innerHTML = '';
                if (data && data.rol === 'admin' && Array.isArray(data.usuarios)) {
                    // Si es admin, muestra todos los usuarios y permite eliminar participantes
                    if (crearUsuarioContainer) crearUsuarioContainer.style.display = 'block';
                    const ul = document.createElement('ul');
                    data.usuarios.forEach(usuario => {
                        const li = document.createElement('li');
                        li.innerHTML = `
                <strong>${usuario.nombre}</strong> (${usuario.email}) - Rol: ${usuario.rol} - Fotos: ${usuario.num_fotos}
                ${usuario.rol === 'participante' ? `<button class="eliminar-usuario" data-id="${usuario.id_usuario}">Eliminar</button>` : ''}
            `;
                        ul.appendChild(li);
                    });
                    usuariosContainer.appendChild(ul);
                } else if (data && data.rol) {
                    // Si es participante, muestra solo sus datos
                    usuariosContainer.innerHTML = `
            <p><strong>Nombre:</strong> ${data.nombre}</p>
            <p><strong>Correo:</strong> ${data.email}</p>
            <p><strong>Número de fotos:</strong> ${data.num_fotos}</p>
        `;
                } else {
                    usuariosContainer.innerHTML = '<p>No hay datos para mostrar.</p>';
                }
            });
    }

    // Permite al admin crear un nuevo usuario participante desde el formulario
    if (crearUsuarioForm) {
        crearUsuarioForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const nombre = document.getElementById('nombre').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            fetch('../Backend/gestion_usuarios.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `nombre=${encodeURIComponent(nombre)}&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert(data.success);
                        location.reload(); // Recarga la página para actualizar la lista
                    } else {
                        alert(data.error || 'Error al crear usuario');
                    }
                });
        });
    }

    // Permite al admin eliminar un usuario participante pulsando el botón correspondiente
    if (usuariosContainer) {
        usuariosContainer.addEventListener('click', function (e) {
            if (e.target.classList.contains('eliminar-usuario')) {
                if (confirm('¿Seguro que deseas eliminar este usuario?')) {
                    const id = e.target.getAttribute('data-id');
                    fetch('../Backend/gestion_usuarios.php', {
                        method: 'DELETE',
                        body: `id_usuario=${id}`
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.success);
                                location.reload(); // Recarga la página tras eliminar
                            } else {
                                alert(data.error || 'Error al eliminar usuario');
                            }
                        });
                }
            }
        });
    }

    // Permite a un participante ver su perfil en un modal (usado en participante.html)
    const verPerfil = document.getElementById('ver-perfil');
    const modal = document.getElementById('modal-ver-perfil');
    const closeBtn = document.querySelector('.close-button');

    if (verPerfil && modal) {
        verPerfil.addEventListener('click', function () {
            fetch('../Backend/gestion_usuarios.php')
                .then(res => res.json())
                .then(data => {
                    if (data && data.nombre) {
                        document.getElementById('perfil-nombre').textContent = data.nombre;
                        document.getElementById('perfil-correo').textContent = data.email;
                        document.getElementById('perfil-num-fotos').textContent = data.num_fotos;
                        modal.style.display = 'block';
                    } else {
                        alert('Hubo un problema al cargar el perfil.');
                    }
                })
                .catch(() => alert('Hubo un problema al cargar el perfil.'));
        });
    }

    // Cierra el modal de perfil al pulsar la X
    if (closeBtn && modal) {
        closeBtn.onclick = function () {
            modal.style.display = 'none';
        };
    }

    // Cierra el modal de perfil al hacer clic fuera del contenido
    if (modal) {
        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        };
    }
});