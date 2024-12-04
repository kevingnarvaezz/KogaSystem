<?php
session_start();
include("../app/config/config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $password = trim($_POST['password']);

    // Consultar el usuario en la base de datos
    $query = $pdo->prepare("SELECT * FROM usuarios WHERE nombre = :nombre AND estado = 1");
    $query->bindParam(':nombre', $nombre);
    $query->execute();
    $usuario = $query->fetch(PDO::FETCH_ASSOC);

    // Validar contraseña
    if ($usuario && $password === $usuario['password']) {
        // Inicio de sesión exitoso
        $_SESSION['id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['apellido'] = $usuario['apellido'];
        $_SESSION['cargo'] = $usuario['cargo'];
    
        // Redirigir a la página de inicio
        header("Location: ../inicio/index.php");
        exit;
    } else {
        // Si las credenciales son incorrectas
        $_SESSION['error'] = "Nombre de usuario o contraseña incorrectos.";
        header("Location: login.php");
        exit;
    }
    
}
