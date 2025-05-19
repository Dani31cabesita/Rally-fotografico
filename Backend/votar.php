<?php

require_once '../utiles/variables.php';
require_once '../utiles/funciones.php';

$mysqli = conectarPDO($host, $user, $password, $bbdd);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_foto'])) {
    echo json_encode(['success' => false, 'message' => 'Petición inválida']);
    exit;
}

$id_foto = intval($_POST['id_foto']);
$ip = $_SERVER['REMOTE_ADDR'];

// Consulta el id_rally de la foto
$stmt = $mysql->prepare("SELECT id_rally FROM fotografias WHERE id_foto=?");
$stmt->bindParam(1, $id_foto, PDO::PARAM_INT);
$stmt->execute();
$id_rally = $stmt->fetchColumn();
if (!$id_rally) {
    echo json_encode(['success' => false, 'message' => 'Foto no encontrada']);
    exit;
}

// Cuenta los votos de esta IP para esta foto
$stmt = $mysql->prepare("SELECT COUNT(*) FROM votaciones WHERE id_foto=? AND ip_votante=?");
$stmt->bindParam(1, $id_foto, PDO::PARAM_INT);
$stmt->bindParam(2, $ip, PDO::PARAM_STR);
$stmt->execute();
$cuenta = $stmt->fetchColumn();

if ($cuenta >= 5) {
    echo json_encode(['success' => false, 'message' => 'Has alcanzado el límite de votos para esta foto desde tu IP.']);
    exit;
}

// Añade el voto
$stmt = $mysql->prepare("INSERT INTO votaciones (id_foto, id_rally, ip_votante) VALUES (?, ?, ?)");
$stmt->bindParam(1, $id_foto, PDO::PARAM_INT);
$stmt->bindParam(2, $id_rally, PDO::PARAM_INT);
$stmt->bindParam(3, $ip, PDO::PARAM_STR);
$stmt->execute();

// Actualiza el contador de votos en la tabla de fotografias
$stmt = $mysql->prepare("UPDATE fotografias SET num_votos = num_votos + 1 WHERE id_foto = ?");
$stmt->bindParam(1, $id_foto, PDO::PARAM_INT);
$stmt->execute();

// Obtiene el nuevo número de votos
$stmt = $mysql->prepare("SELECT num_votos FROM fotografias WHERE id_foto = ?");
$stmt->bindParam(1, $id_foto, PDO::PARAM_INT);
$stmt->execute();
$nuevos_votos = $stmt->fetchColumn();

echo json_encode(['success' => true, 'nuevos_votos' => $nuevos_votos]);