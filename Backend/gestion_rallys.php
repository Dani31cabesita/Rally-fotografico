<?php
//crear y listar rallys activos
require_once '../utiles/funciones.php';
require_once '../utiles/variables.php';

$conexion = conectarPDO($host, $user, $password, $bbdd);

// Finaliza rallys cuya fecha fin ha pasado
$conexion->exec("UPDATE rally SET estado = 'finalizado' WHERE estado = 'activo' AND fecha_fin < CURDATE()");

// Si es POST, intenta crear un nuevo rally
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $fecha_inicio = trim($_POST['fecha_inicio'] ?? '');
    $fecha_fin = trim($_POST['fecha_fin'] ?? '');

    // Comprobar que no falten datos
    if (!$nombre || !$fecha_inicio || !$fecha_fin) {
        http_response_code(400);
        echo json_encode(['error' => 'Faltan datos para crear el rally']);
        exit;
    }

    // Insertar el nuevo rally como activo
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

// Si es GET, devuelve los rallys activos
$sql = "SELECT id_rally, nombre, fecha_inicio, fecha_fin
        FROM rally
        WHERE estado = 'activo'
        ORDER BY fecha_fin ASC";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$rallys = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json'); // Devuelve JSON
echo json_encode($rallys);
exit;
