<?php
require_once '../utiles/funciones.php';
require_once '../utiles/variables.php';

session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_foto = $_POST['id_foto'];

$conexion = conectarPDO($host, $user, $password, $bbdd);

// Verificar si la foto pertenece al usuario
$sql = "SELECT * FROM fotografias WHERE id_foto = :id_foto AND id_usuario = :id_usuario";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(':id_foto', $id_foto);
$stmt->bindParam(':id_usuario', $id_usuario);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado para eliminar esta foto']);
    exit;
}

// Eliminar la foto
$sql = "DELETE FROM fotografias WHERE id_foto = :id_foto";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(':id_foto', $id_foto);

if ($stmt->execute()) {
    echo json_encode(['success' => true]); // Respuesta exitosa
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error al eliminar la foto']);
}
?>