<?php
require_once '../utiles/variables.php';
require_once '../utiles/funciones.php';
$mysqli = conectarPDO($host, $user, $password, $bbdd);

$isAdmin = isset($_GET['admin']) && $_GET['admin'] == 1;

if (!$isAdmin) {
    // Ranking público: solo foto, título y votos
    $stmt = $mysqli->prepare(
        "SELECT titulo, ruta_archivo, num_votos
         FROM fotografias
         WHERE estado = 'admitida'
         ORDER BY num_votos DESC"
    );
    $stmt->execute();
    $fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($fotos);
    exit;
}

// Ranking admin: añade autor y total votos del autor
$stmt = $mysqli->prepare(
    "SELECT f.titulo, f.ruta_archivo, f.num_votos,
            u.nombre AS autor_nombre, u.email AS autor_email,
            (SELECT SUM(num_votos) FROM fotografias WHERE id_usuario = f.id_usuario AND estado = 'admitida') AS votos_autor
     FROM fotografias f
     JOIN usuarios u ON f.id_usuario = u.id_usuario
     WHERE f.estado = 'admitida'
     ORDER BY f.num_votos DESC"
);
$stmt->execute();
$fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($fotos);