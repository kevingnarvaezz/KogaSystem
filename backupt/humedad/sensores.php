<?php
include "controller_humedad.php";

$sensores = obtenerSensores();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Sensores</title>
    <link rel="stylesheet" href="<?php echo $URL?>/humedad/public/styles.css">
    <link rel="stylesheet" href="<?php echo $URL?>/humedad/public/styles_img.css">
    <?php include "../layout/head.php"; ?>
</head>

<body>
    <?php include "../layout/header.php"; ?>

    <main class="container my-5">
        <h1 class="text-center mb-4 textSty">Listado de Sensores</h1>

        <div class="col-12 col*6 d-flex flex-wrap justify-content-center">
            <?php foreach ($sensores as $sensor): ?>
                <div class="sensor-card">
                    <a href="lectura.php?id=<?= $sensor['id_sensor'] ?>">
                        <img src="<?php echo $URL ?>/humedad/public/img/Logo.png" alt="Sensor <?= $sensor['nombre'] ?>" />
                    </a>
                    <h5><?= htmlspecialchars($sensor['nombre']) ?></h5>
                    <p><?= htmlspecialchars($sensor['descripcion']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <?php include "../layout/footer.php"; ?>
</body>

</html>