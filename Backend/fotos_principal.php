<?php
require_once '../utiles/funciones.php';
require_once '../utiles/variables.php';

$conexion = conectarPDO($host, $user, $password, $bbdd);

// Rallys activos con al menos una foto aprobada
$sql = "
SELECT 
    r.id_rally,
    r.nombre AS rally_nombre,
    f.id_foto,
    f.titulo AS foto_titulo,
    f.ruta_archivo,          
    u.nombre AS autor,
    f.num_votos
FROM rally r
JOIN fotografias f ON r.id_rally = f.id_rally
JOIN usuarios u ON f.id_usuario = u.id_usuario
WHERE r.estado = 'activo'
  AND f.estado = 'admitida'
  AND EXISTS (
      SELECT 1 FROM fotografias f2 WHERE f2.id_rally = r.id_rally AND f2.estado = 'admitida'
  )
ORDER BY r.nombre, f.num_votos DESC, f.titulo
";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($datos);
exit;
?>