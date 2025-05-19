document.addEventListener('DOMContentLoaded', function () {
    const rallysLista = document.getElementById('rallys-lista');
    fetch('../Backend/fotos_principal.php')
        .then(res => res.json())
        .then(datos => {
            rallysLista.innerHTML = '';
            if (!Array.isArray(datos) || datos.length === 0) {
                rallysLista.innerHTML = '<p>No hay rallys activos con fotos aprobadas.</p>';
                return;
            }

            // Agrupar por rally
            const agrupados = {};
            datos.forEach(item => {
                if (!agrupados[item.rally_nombre]) agrupados[item.rally_nombre] = [];
                agrupados[item.rally_nombre].push(item);
            });

            Object.keys(agrupados).forEach(rally => {
                const divRally = document.createElement('div');
                divRally.className = 'rally-container';
                divRally.innerHTML = `<h2 class="rally-title">${rally}</h2>`;

                const galeria = document.createElement('div');
                galeria.className = 'rally-gallery';

                agrupados[rally].forEach(foto => {
                    const fotoDiv = document.createElement('div');
                    fotoDiv.className = 'rally-photo';
                    fotoDiv.innerHTML = `
                        <img src="${foto.ruta_archivo}" alt="${foto.foto_titulo}" class="foto-imagen">
                        <div class="photo-title">${foto.foto_titulo}</div>
                        <div class="photo-author">Autor: ${foto.autor}</div>
                        <div class="photo-votes" data-id="${foto.id_foto}">Votos: ${foto.num_votos}</div>
                        <button class="vote-link" data-id="${foto.id_foto}">Votar como favorita</button>
                    `;
                    galeria.appendChild(fotoDiv);
                });

                divRally.appendChild(galeria);
                rallysLista.appendChild(divRally);
            });

            // Añadir funcionalidad de voto
            rallysLista.querySelectorAll('.vote-link').forEach(btn => {
                btn.addEventListener('click', function () {
                    const idFoto = this.getAttribute('data-id');
                    const votosDiv = this.parentElement.querySelector('.photo-votes');
                    fetch('../Backend/votar.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'id_foto=' + encodeURIComponent(idFoto)
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                votosDiv.textContent = 'Votos: ' + data.nuevos_votos;
                                this.disabled = true;
                                this.textContent = '¡Votado!';
                            } else {
                                alert(data.message || 'No se pudo votar.');
                            }
                        });
                });
            });
        })
        .catch(() => {
            rallysLista.innerHTML = '<p>Error al cargar los rallys.</p>';
        });
});