<?php
require_once('../app/config/config.php');  // Conexión a la base de datos
require_once('../libs/fpdf.php');  // Incluir la librería FPDF

// Obtener los filtros desde el formulario
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

// Obtener el ID del usuario que está generando el informe (de la sesión)
session_start();  // Iniciar sesión

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id'])) {
    // Redirigir al usuario a la página de login si no ha iniciado sesión
    header('Location: login.php');
    exit;
}

// Ahora puedes usar $_SESSION['id']
$id_usuario = $_SESSION['id'];
$accion = 'Generacion de Informe de Lectura de Humedad';
$fecha = date('Y-m-d H:i:s');

// Registrar la acción en la tabla historial_acceso
$sql = "INSERT INTO historial_acceso (id_usuario, accion, fecha) VALUES (:id_usuario, :accion, :fecha)";
$stmt_historial = $pdo->prepare($sql);
$stmt_historial->bindParam(':id_usuario', $id_usuario);
$stmt_historial->bindParam(':accion', $accion);
$stmt_historial->bindParam(':fecha', $fecha);

// Ejecutar la consulta para guardar el historial de acceso
$stmt_historial->execute();

// Crear el objeto FPDF
$pdf = new FPDF();
$pdf->AddPage();

// ------------------------- DIBUJAR FONDO EN EL ENCABEZADO -------------------------
// Agregar una imagen en la cabecera
$pdf->Image('public/img/Logo.png', 10, 8, 25); // Ajusta la ruta y el tamaño según tu imagen
$pdf->Image('public/img/Logo.png', 173, 8, 25); // Ajusta la ruta y el tamaño según tu imagen
$pdf->Ln(20); // Ajustar la distancia después de la imagen

// Títulos
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(99, 78, 66);
$pdf->Cell(190, -20, 'Koga System', 0, 1, 'C');
$pdf->Ln(20);
$pdf->SetFont('Arial', 'B', 20);
$pdf->Cell(190, 0, 'Informe de Lecturas de Sensores', 0, 1, 'C');
$pdf->Ln(10);
$pdf->Cell(190, 0, '',  1, 'C');

// Filtros aplicados (solo si están definidos)
$pdf->SetFont('Arial', '', 10);
if ($filter_sensor) {
    $pdf->Cell(30, 6, 'Sensor: ', 0, 0);
    $pdf->Cell(60, 6, $filter_sensor, 0, 1);
}

if ($filter_fecha_desde) {
    $pdf->Cell(30, 6, 'Fecha Desde: ', 0, 0);
    $pdf->Cell(60, 6, $filter_fecha_desde, 0, 1);
}

if ($filter_fecha_hasta) {
    $pdf->Cell(30, 6, 'Fecha Hasta: ', 0, 0);
    $pdf->Cell(60, 6, $filter_fecha_hasta, 0, 1);
}

if ($filter_hora_desde) {
    $pdf->Cell(30, 6, 'Hora Desde: ', 0, 0);
    $pdf->Cell(60, 6, $filter_hora_desde, 0, 1);
}

if ($filter_hora_hasta) {
    $pdf->Cell(30, 6, 'Hora Hasta: ', 0, 0);
    $pdf->Cell(60, 6, $filter_hora_hasta, 0, 1);
}

// ------------------------- DATOS DE LAS LECTURAS -------------------------
$pdf->Ln(10); // Salto de línea
$pdf->SetFont('Arial', 'B', 10);

// Datos de las lecturas
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 6, 'Sensor', 1, 0, 'C');
$pdf->Cell(60, 6, 'Humedad', 1, 0, 'C');
$pdf->Cell(60, 6, 'Fecha de Lectura', 1, 1, 'C');

// Datos de las lecturas
$pdf->SetFont('Arial', '', 10);
foreach ($lecturas as $lectura) {
    $pdf->Cell(60, 6, $lectura['sensor_nombre'], 1, 0, 'C');
    $pdf->Cell(60, 6, $lectura['humedad'], 1, 0, 'C');
    $pdf->Cell(60, 6, $lectura['fecha_lectura'], 1, 1, 'C');
}

// ------------------------- ENVIAR EL PDF AL NAVEGADOR -------------------------

// Cabecera para ver el PDF en el navegador
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="informe_lecturas.pdf"');
header('Content-Transfer-Encoding: binary');

// Enviar el contenido PDF al navegador
$pdf->Output('I', 'informe_lecturas.pdf');
?>
