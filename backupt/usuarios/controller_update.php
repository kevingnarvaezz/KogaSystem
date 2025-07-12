<?php 
include("../app/config/config.php");

// Datos principales del formulario
$id = $_POST['id'];
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$email = $_POST['email'];
$password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;
$ci = $_POST['ci'];
$celular = $_POST['celular'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$genero = $_POST['genero'];
$cargo = $_POST['cargo'];

// Lógica para actualizar la imagen de perfil
$perfil_query = '';
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $nombreperfil = "KogaSystem-" . date("Y-m-d-h-i-s");
    $filename = $nombreperfil . "_" . $_FILES['file']['name'];
    $location = "update_usuarios/" . $filename;

    // Mueve el archivo a la ubicación deseada
    move_uploaded_file($_FILES['file']['tmp_name'], $location);

    // Actualiza el campo `perfil` en la consulta
    $perfil_query = ", perfil = :perfil";
}

// Verificar si se debe cambiar el estado del usuario
if (isset($_POST['cambiar_estado']) && $_POST['cambiar_estado'] == 'true') {
    // Si el formulario incluye el parámetro 'cambiar_estado', actualizamos el estado a 2 (desactivado)
    $estado_query = ", estado = 2";
} else {
    $estado_query = '';  // Si no se cambia el estado, no incluimos este campo en la consulta
}

// Preparar la consulta de actualización
$query_update = $pdo->prepare("
    UPDATE usuarios 
    SET nombre = :nombre, apellido = :apellido, email = :email, ci = :ci, celular = :celular, 
        fecha_nacimiento = :fecha_nacimiento, genero = :genero, cargo = :cargo $perfil_query $estado_query
    WHERE id = :id
");
$query_update->bindParam(':nombre', $nombre);
$query_update->bindParam(':apellido', $apellido);
$query_update->bindParam(':email', $email);
$query_update->bindParam(':ci', $ci);
$query_update->bindParam(':celular', $celular);
$query_update->bindParam(':fecha_nacimiento', $fecha_nacimiento);
$query_update->bindParam(':genero', $genero);
$query_update->bindParam(':cargo', $cargo);
$query_update->bindParam(':id', $id);

// Si se ha subido un nuevo archivo de perfil, lo agregamos a la consulta
if (!empty($perfil_query)) {
    $query_update->bindParam(':perfil', $filename);
}

// Verificar si se debe actualizar la contraseña
if ($password) {
    $query_update->bindParam(':password', $password);
}

// Ejecutar la consulta
if ($query_update->execute()) {
    echo "Usuario actualizado correctamente.";
} else {
    echo "Error al actualizar el usuario.";
}

// Redirigir al listado de usuarios
header("Location: index.php");
exit;
?>
