<?php
include("../app/config/config.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koga System</title>
    <?php include("../layout/head.php"); ?>
    <link rel="stylesheet" href="<?php echo $URL; ?>/inicio/css/styles.css">
</head>
<body>
    <?php include("../layout/header.php"); ?>

    <div class="container">
        <div class="row mbody">
            <div class="col-12 d-flex flex-column justify-content-center align-items-center bodyInfo">
                <img src="<?php echo $URL; ?>/layout/img/Logo.png" alt="Logo">
                <p class="h1 text-center">Koga System</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-md-6">
                <div class="d-grid gap-2">
                    <!-- Botón unificado -->
                    <a href="<?php echo $URL; ?>/monitoreo/sensores.php" class="btn bodyBoton">Monitoreo</a>

                    <!-- Resto de opciones -->
                    <a href="<?php echo $URL; ?>/informes/sensores_lectura.php" class="btn bodyBoton">Informes</a>
                    <a href="<?php echo $URL; ?>/usuarios/index.php" class="btn bodyBoton">Control de Usuarios</a>
                    <a href="<?php echo $URL; ?>/configuraciones/index.php" class="btn bodyBoton">Configuraciones</a>
                </div>
            </div>
        </div>
    </div>

    <?php include("../layout/footer.php"); ?>
</body>
</html>
