<?php
require_once '../utiles/funciones.php';
require_once '../utiles/variables.php';

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../Frontend/login.html");
    exit;
}

$conexion = conectarPDO($host, $user, $password, $bbdd);
$id_usuario = $_SESSION['id_usuario'];
$titulo = $_POST['titulo'] ?? '';
$imagen = $_FILES['imagen'] ?? null;

if (empty($titulo) || !$imagen) {
    die("Todos los campos son obligatorios.");
}

// Obtener el rally activo
$stmt = $conexion->prepare("SELECT * FROM rally WHERE estado = 'activo' ORDER BY id_rally DESC LIMIT 1");
$stmt->execute();
$rally = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no hay rally activo, crear uno
if (!$rally) {
    $nombre = "Rally #1";
    $stmt = $conexion->prepare("INSERT INTO rally (nombre, fecha_inicio, max_fotos_participante, estado) VALUES (:nombre, NOW(), 3, 'activo')");
    $stmt->bindParam(':nombre', $nombre);
    $stmt->execute();
    $id_rally = $conexion->lastInsertId();
} else {
    $id_rally = $rally['id_rally'];

    // Contar fotos en ese rally
    $stmt = $conexion->prepare("SELECT COUNT(*) FROM fotografias WHERE id_rally = :id_rally");
    $stmt->bindParam(':id_rally', $id_rally);
    $stmt->execute();
    $num_fotos = $stmt->fetchColumn();

    if ($num_fotos >= 3) {
        // Finalizar rally actual
        $stmt = $conexion->prepare("UPDATE rally SET estado = 'finalizado', fecha_fin = NOW() WHERE id_rally = :id_rally");
        $stmt->bindParam(':id_rally', $id_rally);
        $stmt->execute();

        // Crear nuevo rally automáticamente
        $stmt = $conexion->prepare("SELECT COUNT(*) FROM rally");
        $stmt->execute();
        $nuevo_numero = $stmt->fetchColumn() + 1; // Incrementar el número del rally
        $nombre = "Rally #$nuevo_numero";

        $stmt = $conexion->prepare("INSERT INTO rally (nombre, fecha_inicio, max_fotos_participante, estado) VALUES (:nombre, NOW(), 3, 'activo')");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->execute();
        $id_rally = $conexion->lastInsertId();
    }
}

// Subir imagen
$directorio = "../uploads/";
if (!is_dir($directorio)) {
    mkdir($directorio, 0777, true);
}
$nombreArchivo = basename($imagen['name']);
$rutaArchivo = $directorio . $nombreArchivo;

if (move_uploaded_file($imagen['tmp_name'], $rutaArchivo)) {
    $stmt = $conexion->prepare("INSERT INTO fotografias (id_usuario, id_rally, titulo, ruta_archivo) VALUES (:id_usuario, :id_rally, :titulo, :ruta_archivo)");
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->bindParam(':id_rally', $id_rally);
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':ruta_archivo', $rutaArchivo);
    $stmt->execute();

    header("Location: ../Frontend/participante.html?mensaje=imagen_subida");
    exit;
} else {
    die("Error al subir la imagen.");
}
?>ALTER TABLE rally MODIFY fecha_fin DATETIME NULL;ALTER TABLE rally MODIFY fecha_fin DATETIME DEFAULT NULL;ALTER TABLE rally MODIFY fecha_fin DATETIME DEFAULT NULL;