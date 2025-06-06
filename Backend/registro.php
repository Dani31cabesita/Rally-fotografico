<?php
//Registro de nuevos usuarios, validando datos y mostrando errores si los hay

require_once '../utiles/funciones.php'; // Funciones auxiliares
require_once '../utiles/variables.php'; // Variables de conexión

// Conectar a la base de datos
$conexion = conectarPDO($host, $user, $password, $bbdd);

// Inicializar variables
$errores = [];
$nombre = obtenerValorCampo('nombre');
$email = obtenerValorCampo('email');
$password = obtenerValorCampo('password');

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar campos obligatorios
    if (!validar_requerido($nombre)) {
        $errores[] = "El campo 'Nombre' es obligatorio.";
    }
    if (!validar_requerido($email) || !validarEmail($email)) {
        $errores[] = "El campo 'Correo' es obligatorio y debe tener un formato válido.";
    }
    if (!validar_requerido($password) || !validarLongitudCadena($password, 6, 255)) {
        $errores[] = "El campo 'Contraseña' es obligatorio y debe tener al menos 6 caracteres.";
    }

    // Si no hay errores, registrar al usuario
    if (empty($errores)) {
        // Verificar si el correo ya está registrado
        $consulta = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $conexion->prepare($consulta);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $errores[] = "El correo ya está registrado.";
        } else {
            // Insertar el usuario en la base de datos
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $insert = "INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)";
            $stmt = $conexion->prepare($insert);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $passwordHash);

            if ($stmt->execute()) {
                header('Location: ../Frontend/principal.html'); // Redirige tras registro exitoso
                exit;
            } else {
                $errores[] = "Error al registrar al usuario. Inténtelo de nuevo.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="../Frontend/estilo/estiloFormularios.css">
</head>

<body>
    <header>
        <h1>Registro</h1>
        <a href="../Frontend/login.html">Acceso</a>
    </header>
    <div class="container">
        <?php if (!empty($errores)): ?>
            <div class="error-messages">
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="POST" action="registro.php">
            <!-- Formulario de registro -->
            <input type="text" name="nombre" placeholder="Nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
            <input type="email" name="email" placeholder="Correo" value="<?php echo htmlspecialchars($email); ?>" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Registrarse</button>
        </form>
    </div>
    <a class="back-link" href="../Frontend/index.html">Volver atrás</a>
</body>

</html>