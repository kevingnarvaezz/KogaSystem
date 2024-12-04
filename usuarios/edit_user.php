<?php 
include ("../app/config/config.php");

$id = $_GET['id'];

$query_usuario = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
$query_usuario->bindParam(':id', $id);
$query_usuario->execute();
$usuario = $query_usuario->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="<?php echo $URL; ?>/usuarios/public/styles.css">
    <?php include("../layout/head.php");?>
</head>
<body>
    <?php include("../layout/header.php");?>

    <main>
        <div class="container">
            <div class="card">
                <div class="card-header cardHeader text-white">
                    Editar Usuario
                </div>
                <div class="card-body">
                    <form action="controller_update.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Nombre</label>
                                    <input type="text" class="form-control" name="nombre" value="<?php echo $usuario['nombre']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Apellido</label>
                                    <input type="text" class="form-control" name="apellido" value="<?php echo $usuario['apellido']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Email</label>
                                    <input type="email" class="form-control" name="email" value="<?php echo $usuario['email']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Contraseña</label>
                                    <input type="password" class="form-control" name="password" placeholder="Dejar vacío para no cambiar">
                                </div>
                                <div class="form-group">
                                    <label for="">Cedula de Identidad</label>
                                    <input type="text" class="form-control" name="ci" value="<?php echo $usuario['ci']; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Celular</label>
                                    <input type="text" class="form-control" name="celular" value="<?php echo $usuario['celular']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control" name="fecha_nacimiento" value="<?php echo $usuario['fecha_nacimiento']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Genero</label>
                                    <select name="genero" id="" class="form-control" required>
                                        <option value="FEMENINO" <?php echo $usuario['genero'] == 'FEMENINO' ? 'selected' : ''; ?>>FEMENINO</option>
                                        <option value="MASCULINO" <?php echo $usuario['genero'] == 'MASCULINO' ? 'selected' : ''; ?>>MASCULINO</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Cargo</label>
                                    <select name="cargo" id="" class="form-control" required>
                                        <option value="ADMINISTRADOR" <?php echo $usuario['cargo'] == 'USUARIO' ? 'selected' : ''; ?>>USUARIO</option>
                                        <option value="SOPORTE" <?php echo $usuario['cargo'] == 'SOPORTE' ? 'selected' : ''; ?>>SOPORTE</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Perfil</label>
                                    <input type="file" class="form-control" id="file" name="file">
                                    <output id="list" style="margin-top: 0px"></output>
                                    <?php if ($usuario['perfil']): ?>
                                        <img src="<?php echo $URL; ?>/usuarios/update_usuarios/<?php echo $usuario['perfil']; ?>" width="100px" alt="">
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-center align-items-center">
                                <input type="submit" class="btn btn-lg btnCreate text-white" value="Guardar Cambios"/>
                                <a href="index.php" class="btn btn-default btn-lg">Cancelar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include("../layout/footer.php");?>
</body>
</html>
