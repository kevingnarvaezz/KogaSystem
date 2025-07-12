<?php
require_once('../app/config/config.php');

// Función para agregar una lectura
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear_lectura'])) {
    $id_sensor = $_POST['id_sensor'];
    $humedad = $_POST['humedad'];
    $fecha_lectura = $_POST['fecha_lectura'];

    // Insertar la nueva lectura
    $sql = "INSERT INTO lecturas (id_sensor, humedad, fecha_lectura) VALUES (:id_sensor, :humedad, :fecha_lectura)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_sensor', $id_sensor);
    $stmt->bindParam(':humedad', $humedad);
    $stmt->bindParam(':fecha_lectura', $fecha_lectura);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Lectura agregada exitosamente.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error al agregar la lectura.</div>";
    }
}

// Obtener todos los sensores para mostrarlos en el formulario
$query_sensores = "SELECT id_sensor, nombre FROM Sensores";
$stmt_sensores = $pdo->query($query_sensores);
$sensores = $stmt_sensores->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Lectura de Sensor</title>
    <?php include "../layout/head.php"; ?>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9aIt2S5v5bI1M+M76xv7Hj7gfbl3hBPzzE8RggY7J4h54G7vhvLfwe8Yarbyl8l5D" crossorigin="anonymous">
</head>

<body>

    <?php include "../layout/header.php"; ?>

    <main>
        <div class="container mt-4">

            <h1 class="text-center">Agregar Lectura de Sensor</h1>

            <!-- Formulario para agregar una lectura -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3>Crear Nueva Lectura</h3>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label for="id_sensor">Seleccionar Sensor:</label>
                            <select id="id_sensor" name="id_sensor" class="form-control" required>
                                <option value="">Seleccionar Sensor</option>
                                <?php foreach ($sensores as $sensor): ?>
                                    <option value="<?= $sensor['id_sensor']; ?>"><?= $sensor['nombre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="humedad">Humedad (%):</label>
                            <input type="number" id="humedad" name="humedad" class="form-control" step="0.01" required>
                        </div>

                        <div class="form-group">
                            <label for="fecha_lectura">Fecha de Lectura:</label>
                            <input type="date" id="fecha_lectura" name="fecha_lectura" class="form-control" required>
                        </div>

                        <button type="submit" name="crear_lectura" class="btn btn-primary">Agregar Lectura</button>
                    </form>
                </div>
            </div>

        </div>

    </main>

    <?php include "../layout/footer.php"; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4rWo0R4Ff5Pbn5K7z0P0ZLKhPMx1tP2h1Un0L5" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.3/dist/umd/popper.min.js" integrity="sha384-nAcmCpQIoGRG5cFj89VFEzFvkZkvZ4CUfSTeO6Hj43v5+BkR6jQhdbWQhEjhDbjd" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-9aIt2S5v5bI1M+M76xv7Hj7gfbl3hBPzzE8RggY7J4h54G7vhvLfwe8Yarbyl8l5D" crossorigin="anonymous"></script>
</body>

</html>