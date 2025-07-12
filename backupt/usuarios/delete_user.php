<?php
include("../app/config/config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    try {
        // Comenzamos una transacción
        $pdo->beginTransaction();

        // Eliminar los registros en historial_acceso que tienen el mismo id_usuario
        $query_delete_historial = $pdo->prepare("DELETE FROM historial_acceso WHERE id_usuario = :id");
        $query_delete_historial->bindParam(':id', $id);
        $query_delete_historial->execute();

        // Eliminar al usuario de la base de datos
        $query_delete_usuario = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
        $query_delete_usuario->bindParam(':id', $id);
        $query_delete_usuario->execute();

        // Confirmar la transacción
        $pdo->commit();

        $_SESSION['message'] = "Usuario eliminado exitosamente.";
    } catch (Exception $e) {
        // Si algo falla, revertir la transacción
        $pdo->rollBack();
        $_SESSION['error'] = "Hubo un error al eliminar el usuario: " . $e->getMessage();
    }

    header("Location: eliminar_usuario.php"); // Redirigir al listado de usuarios
    exit();
}
?>
