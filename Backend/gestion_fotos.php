<?php
require_once '../utiles/funciones.php';
require_once '../utiles/variables.php';
session_start();

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol'])) {
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$conexion = conectarPDO($host, $user, $password, $bbdd);
$rol = $_SESSION['rol'];
$id_usuario = $_SESSION['id_usuario'];

// Cambiar estado de una foto (validar/rechazar) o eliminar si es participante
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_foto = intval($_POST['id_foto'] ?? 0);
    $accion = $_POST['accion'] ?? '';

    if ($rol === 'admin' && in_array($accion, ['admitida', 'rechazada'])) {
        $stmt = $conexion->prepare("UPDATE fotografias SET estado = :estado WHERE id_foto = :id_foto");
        $stmt->bindParam(':estado', $accion);
        $stmt->bindParam(':id_foto', $id_foto);
        $ok = $stmt->execute();
        echo json_encode(['success' => $ok]);
        exit;
    } elseif ($rol === 'participante' && $accion === 'eliminar') {
        // Solo puede eliminar si es suya y no está admitida
        $stmt = $conexion->prepare("DELETE FROM fotografias WHERE id_foto = :id_foto AND id_usuario = :id_usuario AND estado != 'admitida'");
        $stmt->bindParam(':id_foto', $id_foto);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $ok = $stmt->execute();
        echo json_encode(['success' => $ok]);
        exit;
    }
    echo json_encode(['error' => 'Acción no permitida']);
    exit;
}

// GET: Listar fotos
if ($rol === 'admin') {
    $sql = "SELECT f.id_foto, f.titulo, f.estado, f.ruta_archivo, u.nombre AS autor, r.nombre AS rally
            FROM fotografias f
            JOIN usuarios u ON f.id_usuario = u.id_usuario
            JOIN rally r ON f.id_rally = r.id_rally
            ORDER BY f.fecha_subida DESC";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $sql = "SELECT f.id_foto, f.titulo, f.estado, f.ruta_archivo, r.nombre AS rally
            FROM fotografias f
            JOIN rally r ON f.id_rally = r.id_rally
            WHERE f.id_usuario = :id_usuario
            ORDER BY f.fecha_subida DESC";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->execute();
    $fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

header('Content-Type: application/json');
echo json_encode($fotos);
exit;
?>