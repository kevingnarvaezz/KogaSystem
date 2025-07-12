<?php
include ('../app/config/config.php');

/* ---------- Conexión UTF-8 ---------- */
$pdo = new PDO(
    "mysql:dbname=".BD.";host=".SERVIDOR.";charset=utf8",
    USUARIO, PASSWORD,
    [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]
);

/* ---------- Listas de sensores ---------- */
$humedad   = $pdo->query("SELECT id_sensor,nombre FROM sensores WHERE tipo='H'")->fetchAll(PDO::FETCH_ASSOC);
$temperatu = $pdo->query("SELECT id_sensor,nombre FROM sensores WHERE tipo='T'")->fetchAll(PDO::FETCH_ASSOC);

/* ---------- Filtros ---------- */
$fH  = $_GET['sensor_hum']  ?? '';
$fT  = $_GET['sensor_temp'] ?? '';
$fFd = $_GET['fecha_desde'] ?? '';
$fFh = $_GET['fecha_hasta'] ?? '';
$fHd = $_GET['hora_desde']  ?? '';
$fHh = $_GET['hora_hasta']  ?? '';

function filtros(&$sql,&$b,$fd,$fh,$hd,$hh){
    if($fd){ $sql.=" AND DATE(fecha_lectura)>=:fd"; $b[':fd']=$fd; }
    if($fh){ $sql.=" AND DATE(fecha_lectura)<=:fh"; $b[':fh']=$fh; }
    if($hd!==''){ $sql.=" AND HOUR(fecha_lectura)>=:hd"; $b[':hd']=$hd; }
    if($hh!==''){ $sql.=" AND HOUR(fecha_lectura)<=:hh"; $b[':hh']=$hh; }
}

/* ---------- HUMEDAD ---------- */
$lecturasH=[];
if ($fH){
  $sql="SELECT s.nombre sensor, h.humedad, h.fecha_lectura
        FROM lecturas h JOIN sensores s ON s.id_sensor=h.id_sensor
        WHERE h.id_sensor=:id";
  $b=[':id'=>$fH]; filtros($sql,$b,$fFd,$fFh,$fHd,$fHh);
  $sql.=" ORDER BY h.fecha_lectura";
  $st=$pdo->prepare($sql); $st->execute($b);
  $lecturasH=$st->fetchAll(PDO::FETCH_ASSOC);
}

/* ---------- TEMPERATURA ---------- */
$lecturasT=[]; $nombreT='';
if ($fT){
  /* obtenemos nombre del sensor T elegido */
  $nombreT = $pdo->prepare("SELECT nombre FROM sensores WHERE id_sensor=?");
  $nombreT->execute([$fT]); $nombreT = $nombreT->fetchColumn() ?: 'Sensor T';

  $sql="SELECT temperatura,fecha_lectura
        FROM lecturas_temp WHERE id_sensor=:id";
  $b=[':id'=>$fT]; filtros($sql,$b,$fFd,$fFh,$fHd,$fHh);
  $sql.=" ORDER BY fecha_lectura";
  $st=$pdo->prepare($sql); $st->execute($b);
  $lecturasT=$st->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html><html lang="es"><head>
<meta charset="UTF-8">
<title>Lecturas de Sensores</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<?php include("../layout/head.php"); ?>
</head><body>
<?php include("../layout/header.php"); ?>

<main class="container mt-5">
<h1>Filtrar Lecturas de Sensores</h1>

<form method="GET" class="row g-3">
  <div class="col-md-4">
    <label>Sensor de Humedad</label>
    <select name="sensor_hum" class="form-control">
      <option value="">Seleccione Sensor</option>
      <?php foreach($humedad as $s): ?>
        <option value="<?= $s['id_sensor'] ?>" <?= $s['id_sensor']==$fH?'selected':'' ?>>
          <?= htmlspecialchars($s['nombre']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="col-md-4">
    <label>Sensor de Temperatura</label>
    <select name="sensor_temp" class="form-control">
      <option value="">Seleccione Sensor</option>
      <?php foreach($temperatu as $s): ?>
        <option value="<?= $s['id_sensor'] ?>" <?= $s['id_sensor']==$fT?'selected':'' ?>>
          <?= htmlspecialchars($s['nombre']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="col-md-4">
    <label>Fecha Desde</label>
    <input type="date" name="fecha_desde" value="<?= $fFd ?>" class="form-control">
  </div>

  <div class="col-md-4">
    <label>Fecha Hasta</label>
    <input type="date" name="fecha_hasta" value="<?= $fFh ?>" class="form-control">
  </div>

  <div class="col-md-4">
    <label>Hora Desde (0-23)</label>
    <input type="number" name="hora_desde" value="<?= $fHd ?>" min="0" max="23" class="form-control">
  </div>

  <div class="col-md-4">
    <label>Hora Hasta (0-23)</label>
    <input type="number" name="hora_hasta" value="<?= $fHh ?>" min="0" max="23" class="form-control">
  </div>

  <div class="col-md-12">
    <button class="btn btn-primary w-100">Filtrar</button>
  </div>
</form>

<form method="GET" action="generar_pdf.php">
  <input type="hidden" name="sensor_hum"  value="<?= $fH ?>">
  <input type="hidden" name="sensor_temp" value="<?= $fT ?>">
  <input type="hidden" name="fecha_desde" value="<?= $fFd ?>">
  <input type="hidden" name="fecha_hasta" value="<?= $fFh ?>">
  <input type="hidden" name="hora_desde"  value="<?= $fHd ?>">
  <input type="hidden" name="hora_hasta"  value="<?= $fHh ?>">
  <button class="btn btn-success w-100 mt-3">Generar Informe PDF</button>
</form>

<!-- ---------------- Tabla Humedad ---------------- -->
<h3 class="mt-5">Resultados Humedad</h3>
<table class="table table-bordered">
<thead><tr><th>Sensor</th><th>Humedad (%)</th><th>Fecha</th></tr></thead>
<tbody>
<?php if($lecturasH): foreach($lecturasH as $r): ?>
  <tr><td><?= htmlspecialchars($r['sensor']) ?></td><td><?= $r['humedad'] ?></td><td><?= $r['fecha_lectura'] ?></td></tr>
<?php endforeach; else: ?>
  <tr><td colspan="3" class="text-center">Sin datos.</td></tr>
<?php endif; ?>
</tbody></table>

<!-- ---------------- Tabla Temperatura ---------------- -->
<h3 class="mt-4">Resultados Temperatura</h3>
<table class="table table-bordered">
<thead><tr><th>Sensor</th><th>Temperatura (°C)</th><th>Fecha</th></tr></thead>
<tbody>
<?php if($lecturasT): foreach($lecturasT as $r): ?>
  <tr><td><?= htmlspecialchars($nombreT) ?></td><td><?= $r['temperatura'] ?></td><td><?= $r['fecha_lectura'] ?></td></tr>
<?php endforeach; else: ?>
  <tr><td colspan="3" class="text-center">Sin datos.</td></tr>
<?php endif; ?>
</tbody></table>

</main>
<?php include("../layout/footer.php"); ?>
</body></html>
