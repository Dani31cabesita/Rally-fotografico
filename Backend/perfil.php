<?php
session_start();
require_once '../utiles/variables.php';
require_once '../utiles/funciones.php';

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['error' => 'No has iniciado sesión']);
    exit;
}

$mysqli = conectarPDO($host, $user, $password, $bbdd);
$id_usuario = $_SESSION['id_usuario'];

// Obtener nombre y correo
$stmt = $mysqli->prepare("SELECT nombre, email FROM usuarios WHERE id_usuario = ?");
$stmt->bindParam(1, $id_usuario, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['error' => 'Usuario no encontrado']);
    exit;
}

// Contar fotos subidas
$stmt = $mysqli->prepare("SELECT COUNT(*) FROM fotografias WHERE id_usuario = ?");
$stmt->bindParam(1, $id_usuario, PDO::PARAM_INT);
$stmt->execute();
$num_fotos = $stmt->fetchColumn();

echo json_encode([
    'nombre' => $user['nombre'],
    'email' => $user['email'],
    'num_fotos' => $num_fotos
]);