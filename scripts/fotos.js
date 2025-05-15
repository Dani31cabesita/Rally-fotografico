document.addEventListener('DOMContentLoaded', function () {
    const fotosContainer = document.getElementById('fotos-container');

    fetch('../Backend/gestion_fotos.php')
        .then(response => response.json())
        .then(fotos => {
            fotosContainer.innerHTML = '';
            if (!Array.isArray(fotos) || fotos.length === 0) {
                fotosContainer.innerHTML = '<p>No hay fotos para mostrar.</p>';
                return;
            }

            fotos.forEach(foto => {
                const fotoDiv = document.createElement('div');
                fotoDiv.classList.add('foto-item');
                fotoDiv.setAttribute('data-id', foto.id_foto);

                let detalles = `
                    <img src="${foto.ruta_archivo}" alt="${foto.titulo}" class="foto-imagen">
                    <div class="foto-detalles">
                        <p><strong>Título:</strong> ${foto.titulo}</p>
                        <p><strong>Estado:</strong> ${foto.estado}</p>
                `;

                // Si es admin, muestra autor y botones de validar/rechazar
                if (foto.autor !== undefined) {
                    detalles += `<p><strong>Autor:</strong> ${foto.autor}</p>
                                 <p><strong>Rally:</strong> ${foto.rally}</p>
                                 <button class="validar-foto" data-accion="admitida">Validar</button>
                                 <button class="rechazar-foto" data-accion="rechazada">Rechazar</button>`;
                } else {
                    // Participante: muestra rally y botón eliminar si no está admitida
                    detalles += `<p><strong>Rally:</strong> ${foto.rally}</p>`;
                    if (foto.estado !== 'admitida') {
                        detalles += `<button class="eliminar-foto" data-accion="eliminar">Eliminar</button>`;
                    }
                }
                detalles += `</div>`;
                fotoDiv.innerHTML = detalles;
                fotosContainer.appendChild(fotoDiv);
            });

            // Delegación de eventos para botones
            fotosContainer.addEventListener('click', function (event) {
                const btn = event.target;
                if (btn.classList.contains('validar-foto') || btn.classList.contains('rechazar-foto') || btn.classList.contains('eliminar-foto')) {
                    const fotoDiv = btn.closest('.foto-item');
                    const idFoto = fotoDiv.getAttribute('data-id');
                    const accion = btn.getAttribute('data-accion');
                    let confirmMsg = '¿Seguro?';
                    if (btn.classList.contains('eliminar-foto')) confirmMsg = '¿Seguro que deseas eliminar esta foto?';

                    if (confirm(confirmMsg)) {
                        fetch('../Backend/gestion_fotos.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: `id_foto=${idFoto}&accion=${accion}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                if (accion === 'eliminar') fotoDiv.remove();
                                else fotoDiv.querySelector('.foto-detalles p:nth-child(2)').innerHTML = `<strong>Estado:</strong> ${accion}`;
                                // Si no quedan fotos, mostrar mensaje
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
});