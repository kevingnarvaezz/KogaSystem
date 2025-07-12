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
    <?php include("../layout/head.php");?>
</head>
<body>
    <?php include("../layout/header.php");?>

    <main>
        <div class="container">
            <div class="card">
                <div class="card-header cardHeader text-white">
                    Crear nuevo usuario
                </div>
                <div class="card-body">
                    <form action="controller_create.php" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Nombre</label>
                                    <input type="text" class="form-control" name="nombre" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Appelido</label>
                                    <input type="text" class="form-control" name="apellido" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Email</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Contraseña</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Cedula de Identidad</label>
                                    <input type="text" class="form-control" name="ci" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                            <div class="form-group">
                                    <label for="">Celular</label>
                                    <input type="text" class="form-control" name="celular" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control" name="fecha_nacimiento" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Genero</label>
                                    <select name="genero" id="" class="form-control" required>
                                        <option value="FEMENINO">FEMENINO</option>
                                        <option value="MASCULINO">MASCULINO</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Cargo</label>
                                    <select name="cargo" id="" class="form-control" required>
                                        <option value="USUARIO">USUARIO</option>
                                        <option value="SOPORTE">SOPORTE</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Perfil</label required>
                                    <input type="file" class="form-control" id="file" name="file">
                                    <output id="list" style="margin-top: 0px"></output>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-center align-items-center">
                                    <input type="submit" class="btn btn-lg btnCreate text-white" value="Registrar"/>
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

<script>
    function archivo(evt) {
        var files = evt.target.files;
        for (var i = 0, f; f = files[i]; i++) {
            if (!f.type.match('image.*')) {
                continue;
            }
            var reader = new FileReader();
            reader.onload = (function (theFile) {
                return function (e) {
                    document.getElementById("list").innerHTML = ['<img class="thumb thumbnail" src="',e.target.result, '" width="200px" title="', escape(theFile.name), '"/>'].join('');
                };
            })(f);
            reader.readAsDataURL(f);
        }
    }
    document.getElementById('file').addEventListener('change', archivo, false);
</script>