<!-- filepath: c:\Users\danie\OneDrive\Desktop\TFG\Backend\carrousel_imagenes.php -->
<?php
require_once '../utiles/funciones.php';
require_once '../utiles/variables.php';

$conexion = conectarPDO($host, $user, $password, $bbdd);

// Incluir el CSS para el diseño
echo '<link rel="stylesheet" href="../Frontend/estilo/estiloRally.css">';

// Obtener todos los rallys
$stmt = $conexion->prepare("SELECT * FROM rally ORDER BY id_rally DESC");
$stmt->execute();
$rallys = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generar el HTML para cada rally
foreach ($rallys as $rally) {
    echo '<div class="rally-container">';
    echo '<h2 class="rally-title">' . htmlspecialchars($rally['nombre']) . '</h2>';

    // Obtener fotos del rally
    $sql = "SELECT f.*, u.nombre AS autor, 
                (SELECT COUNT(*) FROM votaciones v WHERE v.id_foto = f.id_foto) AS votos
            FROM fotografias f
            JOIN usuarios u ON f.id_usuario = u.id_usuario
            WHERE f.id_rally = :id_rally";
    $stmtFotos = $conexion->prepare($sql);
    $stmtFotos->bindParam(':id_rally', $rally['id_rally']);
    $stmtFotos->execute();
    $fotos = $stmtFotos->fetchAll(PDO::FETCH_ASSOC);

    // Generar el contenedor de imágenes
    echo '<div class="rally-gallery">';
    foreach ($fotos as $foto) {
        echo '<div class="rally-photo">';
        
        // Columna izquierda: Título y autor
        echo '<div class="photo-details-left">';
        echo '<p class="image-title"><strong>' . htmlspecialchars($foto['titulo']) . '</strong></p>';
        echo '<p class="image-author">Autor: ' . htmlspecialchars($foto['autor']) . '</p>';
        echo '</div>';
        
        // Columna central: Imagen
        echo '<img src="' . htmlspecialchars($foto['ruta_archivo']) . '" alt="' . htmlspecialchars($foto['titulo']) . '">';
        
        // Columna derecha: Votos y enlace
        echo '<div class="photo-details-right">';
        echo '<p class="image-votes">Votos: ' . htmlspecialchars($foto['votos']) . '</p>';
        echo '<a href="#" class="vote-link">Votar por favorita</a>';
        echo '</div>';
        
        echo '</div>'; // Cerrar rally-photo
    }
    echo '</div>'; // Cerrar rally-gallery
    echo '</div>'; // Cerrar rally-container
}
?>