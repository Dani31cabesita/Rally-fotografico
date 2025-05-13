// Obtener elementos del DOM
const subirImagenContainer = document.getElementById('subir-imagen');
const modal = document.getElementById('modal-subir-imagen');
const closeButton = document.querySelector('.close-button');

// Abrir el modal al hacer clic en el contenedor "Subir Imagen"
subirImagenContainer.addEventListener('click', () => {
    modal.style.display = 'block';
});

// Cerrar el modal al hacer clic en el botón de cierre
closeButton.addEventListener('click', () => {
    modal.style.display = 'none';
});

// Cerrar el modal al hacer clic fuera del contenido del modal
window.addEventListener('click', (event) => {
    if (event.target === modal) {
        modal.style.display = 'none';
    }
});