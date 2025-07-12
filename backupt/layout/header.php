<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../login/login.php");
    exit;
}

?>
<header>
    <?php if (isset($_SESSION['id'])) {
        $usuario_logueado_id = $_SESSION['id']; // ID del usuario logueado
        $usuario_logueado_cargo = $_SESSION['cargo']; // Cargo del usuario logueado
    } else {
        // Si no hay sesión activa, redirige a la página de login.
        header("Location: login.php");
        exit();
    } ?>
    <div class="container py-4">
        <div class="row align-items-end">
            <div class="col-6 col-md-6 d-flex justify-content-start align-items-end headerImg">
                <img src="<?php echo $URL; ?>/layout/img/Logo.png" alt="Logo" class="logo">
                <a href="<?php echo $URL; ?>/usuarios/edit_user.php?id=<?php echo $_SESSION['id']; ?>" class="ms-3">
                    <?php echo $_SESSION['nombre'] . ' ' . $_SESSION['apellido']; ?>
                </a>
            </div>

            <div class="col-6 col-md-6 justify-content-end align-items-end">
                <nav class="navbar navbar-expand-md navbar-light p-0 justify-content-end align-items-end">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse justify-content-end" id="navbarMenu">
                        <ul class="navbar-nav py-3">
                            <li class="nav-item">
                                <a href="<?php echo $URL; ?>/inicio/index.php" class="mx-2">Inicio</a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo $URL ?>/humedad/sensores.php" class="mx-2">Control</a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo $URL ?>/informes/sensores_lectura.php" class="mx-2">Informes</a>
                            </li>
                            <?php
                            // Verificar si el cargo es "Soporte" o si el usuario logueado es el mismo que el de la fila.
                            if ($usuario_logueado_cargo == "SOPORTE") {
                            ?>
                                <li class="nav-item">
                                    <a href="<?php echo $URL ?>/configuraciones/index.php" class="mx-2">Configuraciones</a>
                                </li>
                            <?php
                            }
                            ?>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </div>

    <div class="he3" style="height: 150px; overflow: hidden;">
        <svg viewBox="0 0 500 150" preserveAspectRatio="none" style="height: 110%; width: 100%;">
            <path d="M-0.00,49.85 C150.00,149.60 380.58,-78.24 500.00,49.85 L500.00,149.60 L-0.00,149.60 Z"
                style="stroke: none; fill: #ffff;"></path>
        </svg>
    </div>
</header>