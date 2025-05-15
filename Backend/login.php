<?php
require_once '../utiles/funciones.php';
require_once '../utiles/variables.php';

session_start();

$conexion = conectarPDO($host, $user, $password, $bbdd);

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Consultar el usuario en la base de datos
    $sql = "SELECT * FROM usuarios WHERE email = :email";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario['password'])) {
        // Iniciar sesión
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol']; // Guardar el rol en la sesión

        // Redirigir según el rol del usuario
        if ($usuario['rol'] === 'admin') {
            header('Location: ../Frontend/admin.html');
        } else {
            header('Location: ../Frontend/participante.html');
        }
        exit;
    } else {
        echo "Correo o contraseña incorrectos.";
    }
}
?>