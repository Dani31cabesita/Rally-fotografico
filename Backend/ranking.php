<?php
//Devuelve el ranking de fotos ordenado por votos

require_once '../utiles/variables.php'; // Variables de conexión
require_once '../utiles/funciones.php'; // Funciones auxiliares
$mysqli = conectarPDO($host, $user, $password, $bbdd);

$isAdmin = isset($_GET['admin']) && $_GET['admin'] == 1; // Comprueba si es admin por parámetro GET

if (!$isAdmin) {
    // Ranking público, solo foto, título y votos
    $stmt = $mysqli->prepare(
        "SELECT titulo, ruta_archivo, num_votos
         FROM fotografias
         WHERE estado = 'admitida'
         ORDER BY num_votos DESC"
    );
    $stmt->execute();
    $fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($fotos); // Devuelve solo fotos admitidas ordenadas por votos
    exit;
}

// Ranking admin, añade autor y total votos del autor
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
echo json_encode($fotos); // Devuelve fotos con info de autor y votos totales del autor para admin