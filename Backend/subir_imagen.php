<?php
require_once '../utiles/funciones.php';
require_once '../utiles/variables.php';
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../Frontend/login.html');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$titulo = trim($_POST['titulo'] ?? '');
$id_rally = intval($_POST['id_rally'] ?? 0);

if (!$titulo || !$id_rally || !isset($_FILES['imagen'])) {
    header('Location: ../Frontend/participante.html?error=Faltan datos');
    exit;
}

// Conexión
$conexion = conectarPDO($host, $user, $password, $bbdd);

// Comprobar si el rally tiene menos de 3 fotos (de cualquier estado)
$stmt = $conexion->prepare("SELECT COUNT(*) FROM fotografias WHERE id_rally = :id_rally");
$stmt->bindParam(':id_rally', $id_rally);
$stmt->execute();
$num_fotos = $stmt->fetchColumn();

if ($num_fotos >= 3) {
    header('Location: ../Frontend/participante.html?error=Este rally ya tiene 3 fotos');
    exit;
}

// Guardar imagen
$uploads_dir = '../uploads/';
if (!is_dir($uploads_dir)) mkdir($uploads_dir, 0777, true);

$nombre_archivo = uniqid() . '_' . basename($_FILES['imagen']['name']);
$ruta_archivo = $uploads_dir . $nombre_archivo;

if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_archivo)) {
    header('Location: ../Frontend/participante.html?error=Error al subir la imagen');
    exit;
}

// Insertar en la base de datos (estado pendiente por defecto)
$stmt = $conexion->prepare("INSERT INTO fotografias (id_usuario, id_rally, titulo, ruta_archivo) VALUES (:id_usuario, :id_rally, :titulo, :ruta_archivo)");
$stmt->bindParam(':id_usuario', $id_usuario);
$stmt->bindParam(':id_rally', $id_rally);
$stmt->bindParam(':titulo', $titulo);
$stmt->bindParam(':ruta_archivo', $ruta_archivo);

if ($stmt->execute()) {
    header('Location: ../Frontend/participante.html?success=Imagen subida correctamente');
} else {
    header('Location: ../Frontend/participante.html?error=Error al guardar en la base de datos');
}
exit;
?>