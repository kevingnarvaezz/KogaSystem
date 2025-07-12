<?php
require_once('../app/config/config.php');
require_once('../libs/fpdf.php');

/* ---------- ZONA HORARIA LOCAL ---------- */
date_default_timezone_set('America/Asuncion');   // ← ajusta aquí si tu país usa otra

session_start();
if (!isset($_SESSION['id'])) { header('Location: ../login/login.php'); exit; }

/* ---------- Parámetros recibidos ---------- */
$idH = $_GET['sensor_hum']  ?? '';
$idT = $_GET['sensor_temp'] ?? '';

$fd = $_GET['fecha_desde'] ?? '';
$fh = $_GET['fecha_hasta'] ?? '';
$hd = $_GET['hora_desde']  ?? '';
$hh = $_GET['hora_hasta']  ?? '';

if (!$idH && !$idT) { die('Debes seleccionar al menos un sensor.'); }

/* ---------- Conexión PDO ---------- */
$pdo = new PDO(
    "mysql:dbname=" . BD . ";host=" . SERVIDOR . ";charset=utf8",
    USUARIO, PASSWORD,
    [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]
);

/* ---------- Helper para filtros ---------- */
function filtros(&$sql,&$b,$fd,$fh,$hd,$hh){
    if($fd){ $sql.=" AND DATE(fecha_lectura)>=:fd"; $b[':fd']=$fd; }
    if($fh){ $sql.=" AND DATE(fecha_lectura)<=:fh"; $b[':fh']=$fh; }
    if($hd!==''){ $sql.=" AND HOUR(fecha_lectura)>=:hd"; $b[':hd']=$hd; }
    if($hh!==''){ $sql.=" AND HOUR(fecha_lectura)<=:hh"; $b[':hh']=$hh; }
}

/* ---------- HUMEDAD ---------- */
$tablaH=[];
if ($idH){
  $sql="SELECT s.nombre sensor,h.humedad,h.fecha_lectura
        FROM lecturas h JOIN sensores s ON s.id_sensor=h.id_sensor
        WHERE h.id_sensor=:id";
  $b=[':id'=>$idH]; filtros($sql,$b,$fd,$fh,$hd,$hh);
  $sql.=" ORDER BY h.fecha_lectura";
  $st=$pdo->prepare($sql); $st->execute($b);
  $tablaH=$st->fetchAll(PDO::FETCH_ASSOC);
}

/* ---------- TEMPERATURA ---------- */
$tablaT=[]; $nombreT='';
if ($idT){
  /* nombre sensor temperatura */
  $nombreT = $pdo->prepare("SELECT nombre FROM sensores WHERE id_sensor=?");
  $nombreT->execute([$idT]);
  $nombreT = $nombreT->fetchColumn() ?: 'Sensor T';

  $sql="SELECT temperatura,fecha_lectura FROM lecturas_temp WHERE id_sensor=:id";
  $b=[':id'=>$idT]; filtros($sql,$b,$fd,$fh,$hd,$hh);
  $sql.=" ORDER BY fecha_lectura";
  $st=$pdo->prepare($sql); $st->execute($b);
  $tablaT=$st->fetchAll(PDO::FETCH_ASSOC);
}

/* ---------- Historial de acceso ---------- */
$pdo->prepare(
  "INSERT INTO historial_acceso (id_usuario,accion,fecha)
   VALUES (:uid,'Generación de Informe',NOW())")
    ->execute([':uid'=>$_SESSION['id']]);

/* ---------- PDF ---------- */
$pdf=new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,utf8_decode('Informe de Lecturas de Sensores'),0,1,'C');
$pdf->Ln(4);
$pdf->SetFont('Arial','',11);
$pdf->Cell(0,8,'Generado: '.date('d/m/Y H:i'),0,1);
$pdf->Ln(3);

/* ---------------- Humedad ---------------- */
if($tablaH){
  $pdf->SetFont('Arial','B',12);
  $pdf->Cell(0,8,'Lecturas de Humedad',0,1);
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(60,8,'Sensor',1,0,'C');
  $pdf->Cell(40,8,'Humedad (%)',1,0,'C');
  $pdf->Cell(60,8,'Fecha',1,1,'C');
  $pdf->SetFont('Arial','',10);
  foreach($tablaH as $r){
     $pdf->Cell(60,8,utf8_decode($r['sensor']),1);
     $pdf->Cell(40,8,$r['humedad'],1);
     $pdf->Cell(60,8,$r['fecha_lectura'],1,1);
  }
  $pdf->Ln(4);
}

/* -------------- Temperatura -------------- */
if($tablaT){
  $pdf->SetFont('Arial','B',12);
  $pdf->Cell(0,8,'Lecturas de Temperatura',0,1);
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(60,8,'Sensor',1,0,'C');
  $pdf->Cell(40,8,'Temperatura ('.chr(176).'C)',1,0,'C');
  $pdf->Cell(60,8,'Fecha',1,1,'C');
  $pdf->SetFont('Arial','',10);
  foreach($tablaT as $r){
     $pdf->Cell(60,8,utf8_decode($nombreT),1);
     $pdf->Cell(40,8,$r['temperatura'],1);
     $pdf->Cell(60,8,$r['fecha_lectura'],1,1);
  }
}

/* ---------- Salida ---------- */
header('Content-Type: application/pdf');
$pdf->Output('I','informe_sensores.pdf');
