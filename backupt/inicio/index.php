<?php
include("../app/config/config.php")
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

    <?php if (isset($_SESSION['id'])) {
        $usuario_logueado_id = $_SESSION['id'];
        $usuario_logueado_cargo = $_SESSION['cargo'];
    } else {
        header("Location: login.php");
        exit();
    } ?>

    <body>
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
                        <a href="<?php echo $URL ?>/humedad/sensores.php" class="btn bodyBoton">Control de Humedad</a>
                        <a href="<?php echo $URL ?>/informes/sensores_lectura.php" class="btn bodyBoton">Informes</a>
                        <?php
                        if ($usuario_logueado_cargo == "SOPORTE") {
                        ?>
                            <a href="<?php echo $URL; ?>/usuarios/index.php" class="btn bodyBoton">Control de Usuarios</a>
                            <a href="<?php echo $URL; ?>/configuraciones/index.php" class="btn bodyBoton">Configuraciones</a>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </body>

    <?php include("../layout/footer.php"); ?>
</body>

</html>