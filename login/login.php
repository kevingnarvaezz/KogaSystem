<?php
session_start();
include("../app/config/config.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koga System</title>
    <link rel="stylesheet" href="<?php echo $URL ?>/login/public/styles.css">
    <?php include("../layout/head.php"); ?>
</head>

<body>
    <main>
        <div class="container">
            <div class="row">
                <div class="col d-flex justify-content-center imgKoga">
                    <img src="public/Img/LogoLetras.png" alt="KogaSystem">
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card bgCard">
                        <div class="card-header text-center bgCardLogin text-white">
                            Iniciar Sesión
                        </div>
                        <div class="card-body">
                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger">
                                    <?php
                                    echo $_SESSION['error'];
                                    unset($_SESSION['error']);
                                    ?>
                                </div>
                            <?php endif; ?>
                            <form action="<?php echo $URL ?>/login/login_process.php" method="POST">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Usuario</label>
                                    <input type="text" name="nombre" id="nombre" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <input type="password" name="password" id="password" class="form-control" required>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btnInicio">Iniciar Sesión</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>