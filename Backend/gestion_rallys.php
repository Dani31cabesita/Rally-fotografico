<?php
require_once '../utiles/funciones.php';
require_once '../utiles/variables.php';

$conexion = conectarPDO($host, $user, $password, $bbdd);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $fecha_inicio = trim($_POST['fecha_inicio'] ?? '');
    $fecha_fin = trim($_POST['fecha_fin'] ?? '');

    if (!$nombre || !$fecha_inicio || !$fecha_fin) {
        http_response_code(400);
        echo json_encode(['error' => 'Faltan datos para crear el rally']);
        exit;
    }

    $sql = "INSERT INTO rally (nombre, fecha_inicio, fecha_fin, max_fotos_participante, estado)
            VALUES (:nombre, :fecha_inicio, :fecha_fin, 3, 'activo')";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':fecha_inicio', $fecha_inicio);
    $stmt->bindParam(':fecha_fin', $fecha_fin);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Rally creado correctamente']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al crear el rally']);
    }
    exit;
}

// GET: rallys activos (por fecha y estado)
$sql = "SELECT id_rally, nombre, fecha_inicio, fecha_fin
        FROM rally
        WHERE CURDATE() BETWEEN DATE(fecha_inicio) AND DATE(fecha_fin)
          AND estado = 'activo'
        ORDER BY fecha_fin ASC";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$rallys = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($rallys);
exit;
?>