<?php
require_once('../config/config.php'); 
require('../libs/fpdf.php'); 

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Informe de Lecturas de Sensores', 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página '.$this->PageNo(), 0, 0, 'C');
    }
}


$query = "SELECT s.nombre AS sensor_nombre, l.humedad, l.fecha_lectura
          FROM Lecturas l
          JOIN Sensores s ON l.id_sensor = s.id_sensor";
$stmt = $pdo->query($query);
$lecturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

$pdf->Cell(40, 10, 'Sensor', 1);
$pdf->Cell(40, 10, 'Humedad', 1);
$pdf->Cell(60, 10, 'Fecha de Lectura', 1);
$pdf->Ln();

foreach ($lecturas as $lectura) {
    $pdf->Cell(40, 10, $lectura['sensor_nombre'], 1);
    $pdf->Cell(40, 10, $lectura['humedad'], 1);
    $pdf->Cell(60, 10, $lectura['fecha_lectura'], 1);
    $pdf->Ln();
}


$pdf->Output();
?>
