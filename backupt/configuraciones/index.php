<?php
include("../app/config/config.php")
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koga System</title>
    <?php include("../layout/head.php");?>
    <link rel="stylesheet" href="<?php echo $URL; ?>/configuraciones/css/styles.css">
</head>
<body>
    <?php include("../layout/header.php");?>

    <body>
        <div class="container">
            <h1 class="text-center textH1">Configuraciones</h1>
            <div class="row justify-content-center">
                <div class="col-12 col-md-6">
                    <div class="d-grid gap-2">
                        <a href="<?php echo $URL?>/humedad/config_humedad.php" class="btn bodyBoton">Sensores</a>
                        <a href="<?php echo $URL?>/informes/informe_usuario.php" class="btn bodyBoton">Acción de usuarios</a>
                        <a href="<?php echo $URL?>/usuarios/eliminar_usuario.php" class="btn bodyBoton">Eliminar Usuario</a>
                    </div>
                </div>
            </div>
        </div>
    </body>

    <?php include("../layout/footer.php");?>
</body>
</html>