function cargarRallys() {
    const rallysContainer = document.getElementById('rallys-container');
    fetch('../Backend/gestion_rallys.php')
        .then(response => response.json())
        .then(data => {
            rallysContainer.innerHTML = '';
            if (data.error) {
                rallysContainer.innerHTML = `<p>${data.error}</p>`;
                return;
            }
            if (!Array.isArray(data) || data.length === 0) {
                rallysContainer.innerHTML = '<p>No hay rallys activos.</p>';
                return;
            }
            const ul = document.createElement('ul');
            data.forEach(rally => {
                const li = document.createElement('li');
                li.textContent = `${rally.nombre} (Del ${rally.fecha_inicio} al ${rally.fecha_fin})`;
                ul.appendChild(li);
            });
            rallysContainer.appendChild(ul);
        })
        .catch(error => {
            rallysContainer.innerHTML = '<p>Error al cargar los rallys.</p>';
        });
}

document.addEventListener('DOMContentLoaded', cargarRallys);

document.getElementById('crear-rally-form').addEventListener('submit', function (event) {
    event.preventDefault();
    const nombreRally = document.getElementById('nombre-rally').value;
    const fechaInicioRally = document.getElementById('fecha-inicio-rally').value;
    const fechaFinRally = document.getElementById('fecha-fin-rally').value;

    fetch('../Backend/gestion_rallys.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `nombre=${encodeURIComponent(nombreRally)}&fecha_inicio=${encodeURIComponent(fechaInicioRally)}&fecha_fin=${encodeURIComponent(fechaFinRally)}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.success);
                cargarRallys();
                document.getElementById('crear-rally-form').reset();
            } else {
                alert(data.error || 'Error al crear el rally.');
            }
        })
        .catch(() => alert('Hubo un problema al intentar crear el rally.'));
});