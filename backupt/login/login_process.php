<?php
session_start();
include("../app/config/config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $password = trim($_POST['password']);

    // Consultar el usuario en la base de datos
    $query = $pdo->prepare("SELECT * FROM usuarios WHERE nombre = :nombre AND (estado = 1 OR estado = 2)");
    $query->bindParam(':nombre', $nombre);
    $query->execute();
    $usuario = $query->fetch(PDO::FETCH_ASSOC);

    // Validar si el usuario existe
    if ($usuario) {
        // Verificar si el estado del usuario es 2 (desactivado o bloqueado)
        if ($usuario['estado'] == 2) {
            // Usuario bloqueado
            $_SESSION['error'] = "Tu cuenta está bloqueada. Contacta con soporte.";
            header("Location: login.php");
            exit;
        }

        // Validar la contraseña
        if ($password === $usuario['password']) {
            // Inicio de sesión exitoso
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['apellido'] = $usuario['apellido'];
            $_SESSION['cargo'] = $usuario['cargo'];

            // Registrar el acceso en el historial
            $id_usuario = $usuario['id'];
            $accion = 'Ingreso al sistema';
            $fecha = date('Y-m-d H:i:s');

            // Insertar el registro en la tabla historial_acceso
            $sql = "INSERT INTO historial_acceso (id_usuario, accion, fecha) VALUES (:id_usuario, :accion, :fecha)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id_usuario', $id_usuario);
            $stmt->bindParam(':accion', $accion);
            $stmt->bindParam(':fecha', $fecha);

            if ($stmt->execute()) {
                // Redirigir a la página de inicio si el acceso se registra correctamente
                header("Location: ../inicio/index.php");
                exit;
            } else {
                // Si hubo un error al registrar el acceso
                $_SESSION['error'] = "Hubo un problema al registrar el acceso.";
                header("Location: login.php");
                exit;
            }
        } else {
            // Si la contraseña es incorrecta
            $_SESSION['error'] = "Nombre de usuario o contraseña incorrectos.";
            header("Location: login.php");
            exit;
        }
    } else {
        // Si no se encuentra al usuario con el nombre proporcionado
        $_SESSION['error'] = "Nombre de usuario o contraseña incorrectos.";
        header("Location: login.php");
        exit;
    }
}
