<?php
//Permite a un usuario subir una imagen a un rally, comprobando límites y guardando en la base de datos

require_once '../utiles/funciones.php'; // Funciones auxiliares
require_once '../utiles/variables.php'; // Variables de conexión
session_start(); // Inicia sesión

// Comprueba si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../Frontend/login.html');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$titulo = trim($_POST['titulo'] ?? '');
$id_rally = intval($_POST['id_rally'] ?? 0);

// Comprueba que se han enviado todos los datos necesarios
if (!$titulo || !$id_rally || !isset($_FILES['imagen'])) {
    header('Location: ../Frontend/participante.html?error=Faltan datos');
    exit;
}

// Conexión a la base de datos
$conexion = conectarPDO($host, $user, $password, $bbdd);

// Comprueba si el rally ya tiene 3 fotos (de cualquier usuario y estado)
$stmt = $conexion->prepare("SELECT COUNT(*) FROM fotografias WHERE id_rally = :id_rally");
$stmt->bindParam(':id_rally', $id_rally);
$stmt->execute();
$num_fotos = $stmt->fetchColumn();

if ($num_fotos >= 3) {
    // Si ya hay 3 fotos, no permite subir más
    header('Location: ../Frontend/participante.html?error=Este rally ya tiene 3 fotos');
    exit;
}

// Guardar imagen en el directorio de uploads
$uploads_dir = '../uploads/';
if (!is_dir($uploads_dir)) mkdir($uploads_dir, 0777, true);

$nombre_archivo = uniqid() . '_' . basename($_FILES['imagen']['name']);
$ruta_archivo = $uploads_dir . $nombre_archivo;

// Mueve el archivo subido al directorio destino
if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_archivo)) {
    header('Location: ../Frontend/participante.html?error=Error al subir la imagen');
    exit;
}

// Inserta la foto en la base de datos
$stmt = $conexion->prepare("INSERT INTO fotografias (id_usuario, id_rally, titulo, ruta_archivo) VALUES (:id_usuario, :id_rally, :titulo, :ruta_archivo)");
$stmt->bindParam(':id_usuario', $id_usuario);
$stmt->bindParam(':id_rally', $id_rally);
$stmt->bindParam(':titulo', $titulo);
$stmt->bindParam(':ruta_archivo', $ruta_archivo);

// Si la inserción es correcta, redirige con éxito; si no, muestra error
if ($stmt->execute()) {
    header('Location: ../Frontend/participante.html?success=Imagen subida correctamente');
} else {
    header('Location: ../Frontend/participante.html?error=Error al guardar en la base de datos');
}
exit;
