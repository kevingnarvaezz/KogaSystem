<?php
require_once('../app/config/config.php');

// Si se envían los filtros, se aplican a la consulta.
$where = "";
$params = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Filtros seleccionados
    $usuario_id = $_POST['usuario_id'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    // Construir la cláusula WHERE
    if ($usuario_id != '') {
        $where .= " AND ha.id_usuario = :usuario_id";
        $params[':usuario_id'] = $usuario_id;
    }
    if ($fecha_inicio != '' && $fecha_fin != '') {
        $where .= " AND ha.fecha BETWEEN :fecha_inicio AND :fecha_fin";
        $params[':fecha_inicio'] = $fecha_inicio;
        $params[':fecha_fin'] = $fecha_fin;
    }
}

// Consulta para obtener las acciones realizadas por los usuarios con los filtros
$query = "
    SELECT u.id, u.nombre, u.apellido, ha.accion, ha.fecha
    FROM Usuarios u
    JOIN historial_acceso ha ON u.id = ha.id_usuario
    WHERE 1=1" . $where . " 
    ORDER BY ha.fecha DESC"; // Puedes ordenar por la fecha de la acción si es necesario

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$acciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener todos los usuarios para el filtro del formulario
$usuarios_query = "SELECT id, nombre FROM Usuarios";
$usuarios_result = $pdo->query($usuarios_query);
$usuarios_select = $usuarios_result->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Acciones de Usuarios</title>
    <?php include("../layout/head.php"); ?>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9aIt2S5v5bI1M+M76xv7Hj7gfbl3hBPzzE8RggY7J4h54G7vhvLfwe8Yarbyl8l5D" crossorigin="anonymous">
</head>

<body>

    <?php include("../layout/header.php"); ?>

    <main>
        <div class="container mt-4">
            <h1 class="text-center mb-4">Informe de Acciones de Usuarios</h1>

            <!-- Formulario de filtros -->
            <form method="POST" class="mb-4">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="usuario_id">Seleccionar Usuario</label>
                        <select class="form-control" id="usuario_id" name="usuario_id">
                            <option value="">Seleccionar</option>
                            <?php foreach ($usuarios_select as $usuario): ?>
                                <option value="<?= $usuario['id']; ?>" <?= isset($_POST['usuario_id']) && $_POST['usuario_id'] == $usuario['id'] ? 'selected' : ''; ?>><?= $usuario['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="fecha_inicio">Fecha Inicio</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?= isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : ''; ?>">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="fecha_fin">Fecha Fin</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?= isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : ''; ?>">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Filtrar</button>

                <!-- Botón para generar el informe en PDF -->

            </form>
            <form method="POST" action="generar_pdf_usuario.php" class="mt-4">
                <input type="hidden" name="usuario_id" value="<?= isset($_POST['usuario_id']) ? $_POST['usuario_id'] : ''; ?>">
                <input type="hidden" name="fecha_inicio" value="<?= isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : ''; ?>">
                <input type="hidden" name="fecha_fin" value="<?= isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : ''; ?>">
                <button type="submit" class="btn btn-success">Generar Informe en PDF</button>
            </form>

            <!-- Tabla de acciones realizadas -->
            <table class="table table-bordered table-responsive">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Acción Realizada</th>
                        <th>Fecha de Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($acciones) > 0): ?>
                        <?php foreach ($acciones as $accion): ?>
                            <tr>
                                <td><?= $accion['id']; ?></td>
                                <td><?= $accion['nombre']; ?></td>
                                <td><?= $accion['apellido']; ?></td>
                                <td><?= $accion['accion']; ?></td>
                                <td><?= $accion['fecha']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No se encontraron acciones para los filtros seleccionados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php include("../layout/footer.php"); ?>

    <!-- Incluir jQuery y Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4rWo0R4Ff5Pbn5K7z0P0ZLKhPMx1tP2h1Un0L5" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.3/dist/umd/popper.min.js" integrity="sha384-nAcmCpQIoGRG5cFj89VFEzFvkZkvZ4CUfSTeO6Hj43v5+BkR6jQhdbWQhEjhDbjd" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-9aIt2S5v5bI1M+M76xv7Hj7gfbl3hBPzzE8RggY7J4h54G7vhvLfwe8Yarbyl8l5D" crossorigin="anonymous"></script>
</body>

</html>