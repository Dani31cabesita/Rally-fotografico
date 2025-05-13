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

$conexion = conectarPDO($host, $user, $password, $bbdd);

// Obtener las fotos del usuario
$sql = "SELECT id_foto, titulo, estado, ruta_archivo 
        FROM fotografias 
        WHERE id_usuario = :id_usuario";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario);
$stmt->execute();

$fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Devolver las fotos en formato JSON
echo json_encode($fotos);
?>