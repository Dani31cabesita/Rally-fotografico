<?php
//crear, eliminar, listar y ver perfil para admin y participantes

require_once '../utiles/funciones.php'; // Funciones auxiliares
require_once '../utiles/variables.php'; // Variables de conexión
session_start(); // Inicia sesión

// Comprueba si el usuario está autenticado y tiene rol
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol'])) {
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$conexion = conectarPDO($host, $user, $password, $bbdd);
$rol = $_SESSION['rol'];
$id_usuario = $_SESSION['id_usuario'];

// Crear nuevo participante (solo admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $rol === 'admin') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Comprueba que no falten datos
    if (!$nombre || !$email || !$password) {
        http_response_code(400);
        echo json_encode(['error' => 'Faltan datos']);
        exit;
    }

    // Comprueba si el email ya existe
    $stmt = $conexion->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        http_response_code(409);
        echo json_encode(['error' => 'El email ya está registrado']);
        exit;
    }

    // Inserta el nuevo usuario
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, 'participante')");
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hash);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Usuario creado correctamente']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al crear el usuario']);
    }
    exit;
}

// Eliminar usuario (solo admin)
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && $rol === 'admin') {
    parse_str(file_get_contents("php://input"), $delete_vars);
    $id_borrar = intval($delete_vars['id_usuario'] ?? 0);

    // Comprueba ID válido
    if ($id_borrar <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'ID inválido']);
        exit;
    }

    // No permitir que el admin se elimine a sí mismo
    if ($id_borrar == $id_usuario) {
        http_response_code(403);
        echo json_encode(['error' => 'No puedes eliminarte a ti mismo']);
        exit;
    }

    // Elimina solo participantes
    $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id_usuario = :id_usuario AND rol = 'participante'");
    $stmt->bindParam(':id_usuario', $id_borrar);
    if ($stmt->execute()) {
        echo json_encode(['success' => 'Usuario eliminado']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al eliminar el usuario']);
    }
    exit;
}

// GET: Participante ve sus datos y número de fotos
if ($rol === 'participante') {
    $sql = "SELECT nombre, email, 
            (SELECT COUNT(*) FROM fotografias WHERE id_usuario = :id_usuario) AS num_fotos
            FROM usuarios WHERE id_usuario = :id_usuario";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->execute();
    $datos = $stmt->fetch(PDO::FETCH_ASSOC);
    $datos['rol'] = $rol; // Añade el rol al resultado
    header('Content-Type: application/json');
    echo json_encode($datos);
    exit;
}

// GET: Admin ve todos los usuarios
if ($rol === 'admin') {
    $sql = "SELECT id_usuario, nombre, email, rol,
            (SELECT COUNT(*) FROM fotografias WHERE id_usuario = u.id_usuario) AS num_fotos
            FROM usuarios u
            ORDER BY rol DESC, nombre ASC";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    // Devuelve también el rol del usuario actual
    echo json_encode([
        'rol' => $rol,
        'usuarios' => $usuarios
    ]);
    exit;
}
