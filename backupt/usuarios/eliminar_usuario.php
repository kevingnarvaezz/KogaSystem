<?php
include("../app/config/config.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Usuarios</title>
    <link rel="stylesheet" href="<?php echo $URL; ?>/usuarios/public/styles.css">
    <link rel="stylesheet" href="<?php echo $URL; ?>/usuarios/public/btnModificar.css">
    <?php include("../layout/head.php"); ?>
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

    <main>
        <div class="container">
            <div class="card">
                <div class="card-header cardHeader text-white">
                    Listado de Usuarios
                </div>
                <div class="table-responsive mx-3">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">N°</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Apellido</th>
                                <th scope="col">Email</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $contador_usuarios = 0;
                            // Obtener todos los usuarios, independientemente del estado
                            $query_usuarios = $pdo->prepare("SELECT * FROM usuarios");
                            $query_usuarios->execute();
                            $usuarios = $query_usuarios->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($usuarios as $usuario) {
                                $contador_usuarios++;
                                $id = $usuario['id'];
                                $nombre = $usuario['nombre'];
                                $apellido = $usuario['apellido'];
                                $email = $usuario['email'];
                                $estado = $usuario['estado'];
                                $estado_texto = ($estado == 1) ? 'Activo' : 'Inactivo';
                            ?>
                                <tr>
                                    <th scope="row"><?php echo $contador_usuarios; ?></th>
                                    <td><?php echo $nombre; ?></td>
                                    <td><?php echo $apellido; ?></td>
                                    <td><?php echo $email; ?></td>
                                    <td><?php echo $estado_texto; ?></td>
                                    <td>
                                        <form action="delete_user.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php }; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php include("../layout/footer.php"); ?>
</body>

</html>
