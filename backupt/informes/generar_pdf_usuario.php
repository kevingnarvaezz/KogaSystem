<?php
session_start();  // Asegúrate de iniciar la sesión aquí

require_once('../app/config/config.php');
require_once('../libs/fpdf.php');

// Obtener el ID del usuario que está generando el informe (de la sesión)
if (!isset($_SESSION['id'])) {
    die("Error: No se ha detectado un ID de usuario válido.");
}

$id_usuario = $_SESSION['id'];  // El ID del usuario que generó el informe
$accion = 'Generacion de Informe de Acciones';
$fecha = date('Y-m-d H:i:s');

// Registrar la acción en la tabla historial_acceso
$sql = "INSERT INTO historial_acceso (id_usuario, accion, fecha) VALUES (:id_usuario, :accion, :fecha)";
$stmt_historial = $pdo->prepare($sql);
$stmt_historial->bindParam(':id_usuario', $id_usuario);
$stmt_historial->bindParam(':accion', $accion);
$stmt_historial->bindParam(':fecha', $fecha);

// Ejecutar la consulta para guardar el historial de acceso
$stmt_historial->execute();

// Consulta para obtener las acciones realizadas por los usuarios
$query = "
    SELECT u.id, u.nombre, u.apellido, ha.accion, ha.fecha
    FROM Usuarios u
    JOIN historial_acceso ha ON u.id = ha.id_usuario
    ORDER BY ha.fecha DESC"; // Ordenar las acciones por fecha

$stmt = $pdo->query($query);
$acciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Crear un nuevo documento PDF
$pdf = new FPDF();
$pdf->AddPage();

// -------------------- DIBUJAR CABECERA --------------------

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
$pdf->Cell(190, 0, 'Informe de Acciones de Usuarios', 0, 1, 'C');
$pdf->Ln(10);
$pdf->SetTextColor(0, 0, 0);


$ancho = array(5, 12, 15, 40, 30);

$ancho = array(20, 30, 35, 70, 30);  

// Títulos de las columnas
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell($ancho[0], 8, 'ID', 1, 0, 'C');
$pdf->Cell($ancho[1], 8, 'Nombre', 1, 0, 'C');
$pdf->Cell($ancho[2], 8, 'Apellido', 1, 0, 'C');
$pdf->Cell($ancho[3], 8, 'Accion Realizada', 1, 0, 'C');
$pdf->Cell($ancho[4], 8, 'Fecha de Accion', 1, 1, 'C');

// Establecer una fuente más pequeña para los datos
$pdf->SetFont('Arial', '', 7); // Ajustar la fuente para los datos

// -------------------- DATOS DE LAS ACCIONES -------------------- 
foreach ($acciones as $accion) {
    $pdf->Cell($ancho[0], 8, $accion['id'], 1, 0, 'C');
    $pdf->Cell($ancho[1], 8, $accion['nombre'], 1, 0, 'C');
    $pdf->Cell($ancho[2], 8, $accion['apellido'], 1, 0, 'C');
    $pdf->Cell($ancho[3], 8, $accion['accion'], 1, 0, 'C');
    $pdf->Cell($ancho[4], 8, $accion['fecha'], 1, 1, 'C');
}

// Generar el PDF
$pdf->Output();
?>
