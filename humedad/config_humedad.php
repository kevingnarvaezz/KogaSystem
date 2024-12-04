<?php
include "controller_humedad.php";

$id_sensor = $_GET['id'] ?? null;
$sensor = null;

if ($id_sensor) {
    global $conexion;
    $query = "SELECT * FROM Sensores WHERE id_sensor = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id_sensor);
    $stmt->execute();
    $result = $stmt->get_result();
    $sensor = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dispositivos</title>
    <link rel="stylesheet" href="public/stylesIMG.css">
    <link rel="stylesheet" href="public/styles.css">
    <?php include "../layout/head.php"; ?>
</head>

<body>
    <?php include "../layout/header.php"; ?>

    <main>
        <h1><?= $id_sensor ? "Editar" : "Crear" ?> Sensor</h1>
        <form action="controller_humedad.php" method="post">
            <input type="hidden" name="id_sensor" value="<?= $sensor['id_sensor'] ?? '' ?>">
            <label>Nombre:</label>
            <input type="text" name="nombre" value="<?= $sensor['nombre'] ?? '' ?>" required>
            <label>Descripción:</label>
            <textarea name="descripcion" required><?= $sensor['descripcion'] ?? '' ?></textarea>
            <button type="submit"><?= $id_sensor ? "Actualizar" : "Crear" ?></button>
        </form>
    </main>

    <?php include "../layout/footer.php"; ?>
</body>

</html>