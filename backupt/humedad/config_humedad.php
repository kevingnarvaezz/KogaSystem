<?php
require_once('../app/config/config.php');

// Obtener el id del usuario autenticado (esto debería ser dinámico, por ejemplo, desde la sesión)
$id_usuario = 1; // Aquí debes obtener el ID del usuario autenticado desde tu sistema

// Función para agregar un nuevo sensor
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear_sensor'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $fecha_instalacion = $_POST['fecha_instalacion'];

    // Insertar un nuevo sensor
    $sql = "INSERT INTO Sensores (nombre, descripcion, fecha_instalacion) VALUES (:nombre, :descripcion, :fecha_instalacion)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':fecha_instalacion', $fecha_instalacion);

    if ($stmt->execute()) {
        // Obtener el id del nuevo sensor
        $id_sensor = $pdo->lastInsertId();

        // Registrar la acción en el historial de acceso
        $accion = 'crear sensor';
        $fecha = date('Y-m-d H:i:s');

        $sql_historial = "INSERT INTO historial_acceso (id_usuario, accion, fecha) 
                          VALUES (:id_usuario, :accion, :fecha)";
        $stmt_historial = $pdo->prepare($sql_historial);
        $stmt_historial->bindParam(':id_usuario', $id_usuario);
        $stmt_historial->bindParam(':accion', $accion);
        $stmt_historial->bindParam(':fecha', $fecha);
        $stmt_historial->execute();
    } else {
        echo "<div class='alert alert-danger'>Error al crear el sensor.</div>";
    }
}

// Función para editar un sensor
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar_sensor'])) {
    $id_sensor = $_POST['id_sensor'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $fecha_instalacion = $_POST['fecha_instalacion'];

    // Actualizar en la tabla Sensores
    $sql = "UPDATE Sensores SET nombre = :nombre, descripcion = :descripcion, fecha_instalacion = :fecha_instalacion WHERE id_sensor = :id_sensor";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_sensor', $id_sensor);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':fecha_instalacion', $fecha_instalacion);

    if ($stmt->execute()) {
        // Registrar la acción en el historial de acceso
        $accion = 'editar sensor';
        $fecha = date('Y-m-d H:i:s');

        $sql_historial = "INSERT INTO historial_acceso (id_usuario, accion, fecha) 
                          VALUES (:id_usuario, :accion, :fecha)";
        $stmt_historial = $pdo->prepare($sql_historial);
        $stmt_historial->bindParam(':id_usuario', $id_usuario);
        $stmt_historial->bindParam(':accion', $accion);
        $stmt_historial->bindParam(':fecha', $fecha);
        $stmt_historial->execute();
    } else {
        echo "<div class='alert alert-danger'>Error al actualizar el sensor.</div>";
    }
}

// Función para eliminar un sensor
if (isset($_GET['eliminar_sensor'])) {
    $id_sensor = $_GET['eliminar_sensor'];

    // Primero, eliminar las lecturas asociadas al sensor
    $sql_lecturas = "DELETE FROM lecturas WHERE id_sensor = :id_sensor";
    $stmt_lecturas = $pdo->prepare($sql_lecturas);
    $stmt_lecturas->bindParam(':id_sensor', $id_sensor);

    if ($stmt_lecturas->execute()) {
        // Luego, eliminar el sensor
        $sql_sensor = "DELETE FROM Sensores WHERE id_sensor = :id_sensor";
        $stmt_sensor = $pdo->prepare($sql_sensor);
        $stmt_sensor->bindParam(':id_sensor', $id_sensor);

        // Verificar si la eliminación del sensor fue exitosa
        if ($stmt_sensor->execute()) {
            // Registrar la acción en el historial de acceso
            $accion = 'eliminar sensor';
            $fecha = date('Y-m-d H:i:s');

            $sql_historial = "INSERT INTO historial_acceso (id_usuario, accion, fecha) VALUES (:id_usuario, :accion, :fecha)";
            $stmt_historial = $pdo->prepare($sql_historial);
            $stmt_historial->bindParam(':id_usuario', $id_usuario); // Asegúrate de definir $id_usuario en tu contexto
            $stmt_historial->bindParam(':accion', $accion);
            $stmt_historial->bindParam(':fecha', $fecha);
            $stmt_historial->execute();
        } else {
            echo "<div class='alert alert-danger'>Error al eliminar el sensor.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Error al eliminar las lecturas asociadas.</div>";
    }
}

// Obtener todos los sensores para mostrarlos
$query_sensores = "SELECT id_sensor, nombre, descripcion, fecha_instalacion FROM Sensores";
$stmt_sensores = $pdo->query($query_sensores);
$sensores = $stmt_sensores->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Sensores</title>
    <?php include "../layout/head.php"; ?>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9aIt2S5v5bI1M+M76xv7Hj7gfbl3hBPzzE8RggY7J4h54G7vhvLfwe8Yarbyl8l5D" crossorigin="anonymous">
    <script>
        // Función para mostrar el formulario de edición y ocultar la lista
        function mostrarFormularioEdicion(id_sensor) {
            var formulario = document.getElementById('form_editar_' + id_sensor);
            var sensores = document.getElementById('sensores_lista');
            var otros_formularios = document.querySelectorAll('.formulario_editar');

            // Ocultar todos los formularios de edición
            otros_formularios.forEach(function(form) {
                form.style.display = 'none';
            });

            // Mostrar el formulario de edición correspondiente
            formulario.style.display = 'block';

            // Ocultar la lista de sensores
            sensores.style.display = 'none';
        }

        // Función para cancelar la edición y volver a la lista
        function cancelarEdicion() {
            var sensores = document.getElementById('sensores_lista');
            var formularios = document.querySelectorAll('.formulario_editar');
            sensores.style.display = 'block';
            formularios.forEach(function(form) {
                form.style.display = 'none';
            });
        }
    </script>
</head>

<body>

    <?php include "../layout/header.php"; ?>

    <main>
        <div class="container mt-4">

            <h1 class="text-center">
                <?php echo isset($_GET['editar_sensor']) ? "Editar Sensor" : "Crear Nuevo Sensor"; ?>
            </h1>

            <!-- Formulario para agregar un nuevo sensor -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3>Crear Nuevo Sensor</h3>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label for="nombre">Nombre del Sensor:</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="descripcion">Descripción:</label>
                            <textarea id="descripcion" name="descripcion" class="form-control" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="fecha_instalacion">Fecha de Instalación:</label>
                            <input type="date" id="fecha_instalacion" name="fecha_instalacion" class="form-control" required>
                        </div>

                        <button type="submit" name="crear_sensor" class="btn btn-primary">Crear Sensor</button>
                    </form>
                </div>
            </div>

            <!-- Listado de Sensores -->
            <div class="card mt-4" id="sensores_lista">
                <div class="card-header">
                    <h3>Lista de Sensores</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Fecha de Instalación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sensores as $sensor) { ?>
                                <tr>
                                    <td><?php echo $sensor['nombre']; ?></td>
                                    <td><?php echo $sensor['descripcion']; ?></td>
                                    <td><?php echo $sensor['fecha_instalacion']; ?></td>
                                    <td>
                                        <a href="javascript:void(0);" onclick="mostrarFormularioEdicion(<?php echo $sensor['id_sensor']; ?>)" class="btn btn-warning btn-sm">Editar</a>
                                        <a href="?eliminar_sensor=<?php echo $sensor['id_sensor']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Formularios de edición -->
            <?php foreach ($sensores as $sensor) { ?>
                <div class="formulario_editar" id="form_editar_<?php echo $sensor['id_sensor']; ?>" style="display: none;">
                    <div class="card mt-4">
                        <div class="card-header">
                            <h3>Editar Sensor: <?php echo $sensor['nombre']; ?></h3>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="id_sensor" value="<?php echo $sensor['id_sensor']; ?>">
                                <div class="form-group">
                                    <label for="nombre">Nombre del Sensor:</label>
                                    <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo $sensor['nombre']; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="descripcion">Descripción:</label>
                                    <textarea id="descripcion" name="descripcion" class="form-control" required><?php echo $sensor['descripcion']; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="fecha_instalacion">Fecha de Instalación:</label>
                                    <input type="date" id="fecha_instalacion" name="fecha_instalacion" class="form-control" value="<?php echo $sensor['fecha_instalacion']; ?>" required>
                                </div>

                                <button type="submit" name="editar_sensor" class="btn btn-primary">Guardar Cambios</button>
                                <button type="button" onclick="cancelarEdicion()" class="btn btn-secondary">Cancelar</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php } ?>

        </div>
    </main>

    <?php include "../layout/footer.php"; ?>

</body>

</html>
