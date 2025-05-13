<?php
require_once '../utiles/funciones.php';
require_once '../utiles/variables.php';

// Conectar a la base de datos
$conexion = conectarPDO($host, $user, $password, $bbdd);

// Inicializar variables
$errores = [];
$email = obtenerValorCampo('email');
$password = obtenerValorCampo('password');

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar campos
    if (!validar_requerido($email) || !validarEmail($email)) {
        $errores[] = "El campo 'Correo' es obligatorio y debe tener un formato válido.";
    }
    if (!validar_requerido($password)) {
        $errores[] = "El campo 'Contraseña' es obligatorio.";
    }

    // Si no hay errores, verificar credenciales
    if (empty($errores)) {
        $consulta = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $conexion->prepare($consulta);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar la contraseña
            if (password_verify($password, $usuario['password'])) {
                // Iniciar sesión y guardar variables
                session_start();
                $_SESSION['id_usuario'] = $usuario['id_usuario']; // Guarda el ID del usuario
                $_SESSION['rol'] = $usuario['rol']; // Guarda el rol del usuario

                // Redirigir según el rol
                if ($usuario['rol'] === 'admin') {
                    header('Location: ../Frontend/admin.html');
                } elseif ($usuario['rol'] === 'participante') {
                    header('Location: ../Frontend/participante.html');
                }
                exit;
            } else {
                $errores[] = "Contraseña incorrecta.";
            }
        } else {
            $errores[] = "No se encontró una cuenta con ese correo.";
        }
    }
}
?>