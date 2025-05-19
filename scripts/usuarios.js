document.addEventListener('DOMContentLoaded', function () {
    // Gestión de usuarios (admin y participante)
    const usuariosContainer = document.getElementById('usuarios-container');
    const crearUsuarioContainer = document.getElementById('crear-usuario-container');
    const crearUsuarioForm = document.getElementById('crear-usuario-form');

    if (usuariosContainer) {
        fetch('../Backend/gestion_usuarios.php')
            .then(res => res.json())
            .then(data => {
                usuariosContainer.innerHTML = '';
                if (data && data.rol === 'admin' && Array.isArray(data.usuarios)) {
                    // Admin: mostrar todos los usuarios
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
                    // Participante: mostrar solo sus datos
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

    // Crear usuario (solo admin)
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
                        location.reload();
                    } else {
                        alert(data.error || 'Error al crear usuario');
                    }
                });
        });
    }

    // Eliminar usuario (solo admin)
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
                                location.reload();
                            } else {
                                alert(data.error || 'Error al eliminar usuario');
                            }
                        });
                }
            }
        });
    }

    // Mostrar perfil en modal al pulsar "Ver Perfil" (participante.html)
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

    if (closeBtn && modal) {
        closeBtn.onclick = function () {
            modal.style.display = 'none';
        };
    }

    // Cerrar modal al hacer clic fuera
    if (modal) {
        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        };
    }
});