<?php
require_once('../app/config/config.php');  // Incluye la conexión a la base de datos

// Cambiar los parámetros de la conexión para usar UTF-8 y asegurar compatibilidad con el servidor remoto.
$servidor = "mysql:dbname=" . BD . ";host=" . SERVIDOR . ";charset=utf8";

try {
    $pdo = new PDO($servidor, USUARIO, PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
} catch (PDOException $e) {
    echo "<script>alert('Error en la conexión de la base de datos: " . $e->getMessage() . "')</script>";
    exit();
}

// Obtener lista de sensores para el filtro
$query_sensores = "SELECT id_sensor, nombre FROM Sensores";
$stmt_sensores = $pdo->query($query_sensores);
$sensores = $stmt_sensores->fetchAll(PDO::FETCH_ASSOC);

// Recibir los filtros enviados desde el formulario
$filter_sensor = isset($_GET['sensor']) ? $_GET['sensor'] : '';
$filter_fecha_desde = isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : '';
$filter_fecha_hasta = isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : '';
$filter_hora_desde = isset($_GET['hora_desde']) ? $_GET['hora_desde'] : '';
$filter_hora_hasta = isset($_GET['hora_hasta']) ? $_GET['hora_hasta'] : '';

// Consulta base
$query = "SELECT s.nombre AS sensor_nombre, l.humedad, l.fecha_lectura
          FROM Lecturas l
          JOIN Sensores s ON l.id_sensor = s.id_sensor
          WHERE 1=1";

// Aplicar filtros a la consulta
if ($filter_sensor) {
    $query .= " AND l.id_sensor = :sensor";
}

if ($filter_fecha_desde) {
    $query .= " AND DATE(l.fecha_lectura) >= :fecha_desde";
}

if ($filter_fecha_hasta) {
    $query .= " AND DATE(l.fecha_lectura) <= :fecha_hasta";
}

if ($filter_hora_desde) {
    $query .= " AND HOUR(l.fecha_lectura) >= :hora_desde";
}

if ($filter_hora_hasta) {
    $query .= " AND HOUR(l.fecha_lectura) <= :hora_hasta";
}

// Ejecutar la consulta
$stmt = $pdo->prepare($query);

if ($filter_sensor) {
    $stmt->bindParam(':sensor', $filter_sensor, PDO::PARAM_INT);
}

if ($filter_fecha_desde) {
    $stmt->bindParam(':fecha_desde', $filter_fecha_desde, PDO::PARAM_STR);
}

if ($filter_fecha_hasta) {
    $stmt->bindParam(':fecha_hasta', $filter_fecha_hasta, PDO::PARAM_STR);
}

if ($filter_hora_desde) {
    $stmt->bindParam(':hora_desde', $filter_hora_desde, PDO::PARAM_INT);
}

if ($filter_hora_hasta) {
    $stmt->bindParam(':hora_hasta', $filter_hora_hasta, PDO::PARAM_INT);
}

$stmt->execute();
$lecturas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturas de Sensores</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <?php include("../layout/head.php"); ?>
</head>

<body>
    <?php include("../layout/header.php"); ?>


    <main>
        <div class="container mt-5">
            <h1>Filtrar Lecturas de Sensores</h1>

            <!-- Formulario de Filtros -->
            <form method="GET" action="sensores_lectura.php">
                <div class="form-row">
                    <div class="col-md-4">
                        <label for="sensor">Sensor</label>
                        <select class="form-control" id="sensor" name="sensor">
                            <option value="">Seleccione Sensor</option>
                            <?php foreach ($sensores as $sensor) { ?>
                                <option value="<?php echo $sensor['id_sensor']; ?>" <?php echo ($sensor['id_sensor'] == $filter_sensor) ? 'selected' : ''; ?>>
                                    <?php echo $sensor['nombre']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="fecha_desde">Fecha Desde (YYYY-MM-DD)</label>
                        <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" value="<?php echo $filter_fecha_desde; ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="fecha_hasta">Fecha Hasta (YYYY-MM-DD)</label>
                        <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" value="<?php echo $filter_fecha_hasta; ?>">
                    </div>
                </div>

                <div class="form-row mt-3">
                    <div class="col-md-4">
                        <label for="hora_desde">Hora Desde (0-23)</label>
                        <input type="number" class="form-control" id="hora_desde" name="hora_desde" min="0" max="23" value="<?php echo $filter_hora_desde; ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="hora_hasta">Hora Hasta (0-23)</label>
                        <input type="number" class="form-control" id="hora_hasta" name="hora_hasta" min="0" max="23" value="<?php echo $filter_hora_hasta; ?>">
                    </div>

                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary mt-4">Filtrar</button>
                    </div>
                </div>
            </form>

            <!-- Botón para generar PDF -->
            <form method="GET" action="generar_pdf.php">
                <input type="hidden" name="sensor" value="<?php echo $filter_sensor; ?>">
                <input type="hidden" name="fecha_desde" value="<?php echo $filter_fecha_desde; ?>">
                <input type="hidden" name="fecha_hasta" value="<?php echo $filter_fecha_hasta; ?>">
                <input type="hidden" name="hora_desde" value="<?php echo $filter_hora_desde; ?>">
                <input type="hidden" name="hora_hasta" value="<?php echo $filter_hora_hasta; ?>">
                <button type="submit" class="btn btn-success mt-4">Generar Informe PDF</button>
            </form>

            <!-- Mostrar los resultados -->
            <h3 class="mt-5">Resultados</h3>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Sensor</th>
                        <th>Humedad</th>
                        <th>Fecha de Lectura</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($lecturas) > 0) { ?>
                        <?php foreach ($lecturas as $lectura) { ?>
                            <tr>
                                <td><?php echo $lectura['sensor_nombre']; ?></td>
                                <td><?php echo $lectura['humedad']; ?></td>
                                <td><?php echo $lectura['fecha_lectura']; ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="3" class="text-center">No se encontraron lecturas.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php include("../layout/footer.php");?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
