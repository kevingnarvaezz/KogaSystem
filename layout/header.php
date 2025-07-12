<?php
/* Iniciar sesión solo si aún no existe */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id'])) {
    header("Location: ../login/login.php");
    exit;
}
?>
<header>
    <div class="container py-4">
        <div class="row align-items-end">
            <div class="col-6 col-md-6 d-flex justify-content-start align-items-end headerImg">
                <img src="<?php echo $URL; ?>/layout/img/Logo.png" alt="Logo" class="logo" style="max-width: 150px;">
                <a href="<?php echo $URL; ?>/usuarios/edit_user.php?id=<?php echo $_SESSION['id']; ?>" class="ms-3">
                    <?php echo $_SESSION['nombre'] . ' ' . $_SESSION['apellido']; ?>
                </a>
            </div>

            <div class="col-6 col-md-6 justify-content-end align-items-end">
                <nav class="navbar navbar-expand-lg navbar-light p-0 justify-content-end align-items-end">
                    <!-- Botón hamburguesa -->
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu" 
                        aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarMenu">
                        <ul class="navbar-nav py-3">
                            <li class="nav-item">
                                <a href="<?php echo $URL; ?>/inicio/index.php" class="mx-2">Inicio</a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo $URL; ?>/monitoreo/sensores.php" class="mx-2">Monitoreo</a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo $URL; ?>/informes/sensores_lectura.php" class="mx-2">Informes</a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo $URL; ?>/configuraciones/index.php" class="mx-2">Configuraciones</a>
                            </li>
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

<!-- Bootstrap JS y CSS (si no están ya en layout/head.php) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<style>
/* Menú hamburguesa en móviles */
@media (max-width: 991px) {
    .navbar-collapse {
        position: fixed;
        top: 0; left: 0; right: 0;
        background-color: rgba(0,0,0,0.9);
        height: 100vh;
        overflow-y: auto;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        padding-top: 20px;
    }

    .navbar-nav { width: 100%; text-align: center; }
    .navbar-nav .nav-item { width: 100%; margin: 10px 0; }
    .navbar-nav .nav-item a {
        color: #fff;
        font-size: 18px;
        text-decoration: none;
        padding: 10px 0; display: block;
    }

    .navbar-toggler { z-index: 99999; }
}
</style>
