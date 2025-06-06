//Gestiona la visualización y administración de fotos para admin y participantes

document.addEventListener('DOMContentLoaded', function () {
    const fotosContainer = document.getElementById('fotos-container');

    // Solicita al backend la lista de fotos (según el rol, devuelve todas o solo las del usuario)
    fetch('../Backend/gestion_fotos.php')
        .then(response => response.json())
        .then(fotos => {
            fotosContainer.innerHTML = '';
            if (!Array.isArray(fotos) || fotos.length === 0) {
                fotosContainer.innerHTML = '<p>No hay fotos para mostrar.</p>';
                return;
            }

            // Recorre cada foto y la muestra en el contenedor
            fotos.forEach(foto => {
                const fotoDiv = document.createElement('div');
                fotoDiv.classList.add('foto-item');
                fotoDiv.setAttribute('data-id', foto.id_foto);

                // Construye los detalles de la foto (diferente para admin y participante)
                let detalles = `
                    <img src="${foto.ruta_archivo}" alt="${foto.titulo}" class="foto-imagen">
                    <div class="foto-detalles">
                        <p><strong>Título:</strong> ${foto.titulo}</p>
                        <p><strong>Estado:</strong> ${foto.estado}</p>
                `;

                // Si es admin, muestra autor, rally y botones de validar/rechazar
                if (foto.autor !== undefined) {
                    detalles += `<p><strong>Autor:</strong> ${foto.autor}</p>
                                 <p><strong>Rally:</strong> ${foto.rally}</p>
                                 <button class="validar-foto" data-accion="admitida">Validar</button>
                                 <button class="rechazar-foto" data-accion="rechazada">Rechazar</button>`;
                } else {
                    // Si es participante, muestra rally y botón eliminar si la foto no está admitida
                    detalles += `<p><strong>Rally:</strong> ${foto.rally}</p>`;
                    if (foto.estado !== 'admitida') {
                        detalles += `<button class="eliminar-foto" data-accion="eliminar">Eliminar</button>`;
                    }
                }
                detalles += `</div>`;
                fotoDiv.innerHTML = detalles;
                fotosContainer.appendChild(fotoDiv);
            });

            // Delegación de eventos: gestiona clicks en botones de validar, rechazar o eliminar
            fotosContainer.addEventListener('click', function (event) {
                const btn = event.target;
                if (btn.classList.contains('validar-foto') || btn.classList.contains('rechazar-foto') || btn.classList.contains('eliminar-foto')) {
                    const fotoDiv = btn.closest('.foto-item');
                    const idFoto = fotoDiv.getAttribute('data-id');
                    const accion = btn.getAttribute('data-accion');
                    let confirmMsg = '¿Seguro?';
                    if (btn.classList.contains('eliminar-foto')) confirmMsg = '¿Seguro que deseas eliminar esta foto?';

                    // Confirma la acción antes de enviar la petición
                    if (confirm(confirmMsg)) {
                        fetch('../Backend/gestion_fotos.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: `id_foto=${idFoto}&accion=${accion}`
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Si se elimina, borra el div; si se valida/rechaza, actualiza el estado
                                    if (accion === 'eliminar') fotoDiv.remove();
                                    else fotoDiv.querySelector('.foto-detalles p:nth-child(2)').innerHTML = `<strong>Estado:</strong> ${accion}`;
                                    // Si no quedan fotos, muestra mensaje
                                    if (!fotosContainer.querySelector('.foto-item')) {
                                        fotosContainer.innerHTML = '<p>No hay fotos para mostrar.</p>';
                                    }
                                } else {
                                    alert(data.error || 'Error en la operación');
                                }
                            })
                            .catch(() => alert('Error en la operación'));
                    }
                }
            });
        })
        .catch(() => {
            fotosContainer.innerHTML = '<p>Error al cargar las fotos.</p>';
        });

    // Botón volver: redirige al panel correspondiente según el rol (admin o participante)
    const volverBtn = document.getElementById('volver-btn');
    if (volverBtn) {
        volverBtn.addEventListener('click', function (e) {
            e.preventDefault();
            // Consulta el rol actual para decidir a dónde volver
            fetch('../Backend/gestion_usuarios.php')
                .then(res => res.json())
                .then(data => {
                    if (data && data.rol === 'admin') {
                        window.location.href = 'admin.html';
                    } else {
                        window.location.href = 'participante.html';
                    }
                })
                .catch(() => window.location.href = 'participante.html');
        });
    }
});