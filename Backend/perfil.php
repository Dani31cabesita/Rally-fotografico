<?php
//Devuelve los datos del perfil del usuario logueado (nombre, email, num de fotos)

session_start(); // Inicia sesión
require_once '../utiles/variables.php'; // Variables de conexión
require_once '../utiles/funciones.php'; // Funciones auxiliares

// Comprueba si hay usuario logueado
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['error' => 'No has iniciado sesión']);
    exit;
}

$mysqli = conectarPDO($host, $user, $password, $bbdd);
$id_usuario = $_SESSION['id_usuario'];

// Obtiene nombre y correo del usuario
$stmt = $mysqli->prepare("SELECT nombre, email FROM usuarios WHERE id_usuario = ?");
$stmt->bindParam(1, $id_usuario, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no existe el usuario, devuelve error
if (!$user) {
    echo json_encode(['error' => 'Usuario no encontrado']);
    exit;
}

// Cuenta el número de fotos subidas por el usuario
$stmt = $mysqli->prepare("SELECT COUNT(*) FROM fotografias WHERE id_usuario = ?");
$stmt->bindParam(1, $id_usuario, PDO::PARAM_INT);
$stmt->execute();
$num_fotos = $stmt->fetchColumn();

// Devuelve los datos en formato JSON
echo json_encode([
    'nombre' => $user['nombre'],
    'email' => $user['email'],
    'num_fotos' => $num_fotos
]);
