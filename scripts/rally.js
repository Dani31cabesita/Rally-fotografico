//Gestiona la visualización y creación de rallys desde el panel de administración

// Función para cargar y mostrar los rallys activos
function cargarRallys() {
    const rallysContainer = document.getElementById('rallys-container');
    // Solicita al backend la lista de rallys activos
    fetch('../Backend/gestion_rallys.php')
        .then(response => response.json())
        .then(data => {
            rallysContainer.innerHTML = '';
            if (data.error) {
                // Muestra error si lo hay
                rallysContainer.innerHTML = `<p>${data.error}</p>`;
                return;
            }
            if (!Array.isArray(data) || data.length === 0) {
                // Si no hay rallys, muestra mensaje
                rallysContainer.innerHTML = '<p>No hay rallys activos.</p>';
                return;
            }
            // Crea una lista con los rallys activos
            const ul = document.createElement('ul');
            data.forEach(rally => {
                const li = document.createElement('li');
                li.textContent = `${rally.nombre} (Del ${rally.fecha_inicio} al ${rally.fecha_fin})`;
                ul.appendChild(li);
            });
            rallysContainer.appendChild(ul);
        })
        .catch(error => {
            // Si hay error en la petición, muestra mensaje
            rallysContainer.innerHTML = '<p>Error al cargar los rallys.</p>';
        });
}

// Carga los rallys al cargar la página
document.addEventListener('DOMContentLoaded', cargarRallys);

// Maneja el envío del formulario para crear un nuevo rally
document.getElementById('crear-rally-form').addEventListener('submit', function (event) {
    event.preventDefault();
    const nombreRally = document.getElementById('nombre-rally').value;
    const fechaInicioRally = document.getElementById('fecha-inicio-rally').value;
    const fechaFinRally = document.getElementById('fecha-fin-rally').value;

    // Envía los datos del nuevo rally al backend
    fetch('../Backend/gestion_rallys.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `nombre=${encodeURIComponent(nombreRally)}&fecha_inicio=${encodeURIComponent(fechaInicioRally)}&fecha_fin=${encodeURIComponent(fechaFinRally)}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Si se crea correctamente, muestra mensaje, recarga la lista y resetea el formulario
                alert(data.success);
                cargarRallys();
                document.getElementById('crear-rally-form').reset();
            } else {
                // Si hay error, muestra mensaje
                alert(data.error || 'Error al crear el rally.');
            }
        })
        .catch(() => alert('Hubo un problema al intentar crear el rally.'));
});