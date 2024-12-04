<?php 
include ("../app/config/config.php")
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="<?php echo $URL; ?>/usuarios/public/styles.css">
    <link rel="stylesheet" href="<?php echo $URL; ?>/usuarios/public/btnModificar.css">
    <?php include("../layout/head.php");?>
</head>
<body>
    <?php include("../layout/header.php");?>

    <main>
        <div class="container">
            <div class="row">
                <div class="col d-flex justify-content-end">
                    <a href="<?php echo $URL?>/usuarios/create.php" class="btnBodyCreate">Crear nuevo usuario</a>
                </div>
            </div>
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
                                <th scope="col">Cargo</th>
                                <th scope="col">Perfil</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $contador_usuarios = 0;
                            $query_usuarios = $pdo->prepare("SELECT * FROM usuarios");
                            $query_usuarios->execute();
                            $usuarios = $query_usuarios->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($usuarios as $usuario) {
                                $id = $usuario['id'];
                                $nombre = $usuario['nombre'];
                                $apellido = $usuario['apellido'];
                                $email = $usuario['email'];
                                $cargo = $usuario['cargo'];
                                $perfil = $usuario['perfil'];
                                $genero  = $usuario['genero'];
                                $contador_usuarios++;
                            ?>
                            <tr>
                                <th scope="row"><?php echo $contador_usuarios; ?></th>
                                <td><?php echo $nombre; ?></td>
                                <td><?php echo $apellido; ?></td>
                                <td><?php echo $email; ?></td>
                                <td><?php echo $cargo; ?></td>
                                <td>
                                    <?php
                                    $caracter_buscar = ".";
                                    $buscar_caracter = strpos($perfil, $caracter_buscar);
                                    if ($buscar_caracter == true) {
                                        ?>
                                        <img src="<?php echo $URL; ?>/usuarios/update_usuarios/<?php echo $perfil; ?>" width="50px" alt="">
                                        <?php
                                    } else {
                                        if ($genero == "MASCULINO") {
                                        ?>
                                        <img src="<?php echo $URL; ?>/usuarios/public/img/hombres.png" width="50px" alt="">
                                        <?php
                                        } else {
                                        ?>
                                        <img src="<?php echo $URL; ?>/usuarios/public/img/mujeres.png" width="50px" alt="">
                                        <?php
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="edit_user.php?id=<?php echo $id; ?>" class="modificarBtn d-flex justify-content-center">Modificar</a>
                                </td>
                            </tr>
                            <?php }; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>


    <?php include("../layout/footer.php");?>
</body>
</html>