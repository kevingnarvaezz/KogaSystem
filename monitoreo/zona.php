<?php
require_once '../app/config/config.php';
if (session_status()===PHP_SESSION_NONE){ session_start(); }
if (!isset($_SESSION['id'])){ header('Location: ../login/login.php'); exit; }

$idh = $_GET['idh'] ?? '';   // sensor de Humedad
$idt = $_GET['idt'] ?? '';   // sensor de Temperatura

/* --- listas para los <select> --- */
$listaH = $pdo->query("SELECT id_sensor,nombre FROM sensores WHERE tipo='H'")->fetchAll(PDO::FETCH_ASSOC);
$listaT = $pdo->query("SELECT id_sensor,nombre FROM sensores WHERE tipo='T'")->fetchAll(PDO::FETCH_ASSOC);

/* --- datos de las últimas 24 h (si ya vienen los dos IDs) --- */
$h24 = $t24 = [];
if ($idh && $idt) {
    $sqlH = "SELECT DATE_FORMAT(fecha_lectura,'%H:%i') h, humedad
             FROM lecturas WHERE id_sensor=? AND fecha_lectura>=NOW()-INTERVAL 24 HOUR
             ORDER BY fecha_lectura";
    $sqlT = "SELECT DATE_FORMAT(fecha_lectura,'%H:%i') h, temperatura
             FROM lecturas_temp WHERE id_sensor=? AND fecha_lectura>=NOW()-INTERVAL 24 HOUR
             ORDER BY fecha_lectura";
    $h24  = $pdo->prepare($sqlH); $h24->execute([$idh]); $h24 = $h24->fetchAll(PDO::FETCH_ASSOC);
    $t24  = $pdo->prepare($sqlT); $t24->execute([$idt]); $t24 = $t24->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html><html lang="es"><head>
<meta charset="utf-8"><title>Comparar Zona – Humedad & Temperatura</title>
<?php include "../layout/head.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head><body>
<?php include "../layout/header.php"; ?>

<main class="container py-4">
  <h1 class="text-center mb-4">Comparar Humedad y Temperatura (Últimas 24 h)</h1>

  <!-- Form selección -->
  <form class="row g-3 mb-5" method="GET">
    <div class="col-md-5">
      <label class="form-label">Sensor de Humedad</label>
      <select name="idh" class="form-select" required>
        <option value="">— elegir —</option>
        <?php foreach ($listaH as $s): ?>
          <option value="<?= $s['id_sensor'] ?>" <?= $s['id_sensor']==$idh?'selected':'' ?>>
            <?= htmlspecialchars($s['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-5">
      <label class="form-label">Sensor de Temperatura</label>
      <select name="idt" class="form-select" required>
        <option value="">— elegir —</option>
        <?php foreach ($listaT as $s): ?>
          <option value="<?= $s['id_sensor'] ?>" <?= $s['id_sensor']==$idt?'selected':'' ?>>
            <?= htmlspecialchars($s['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-2 d-flex align-items-end">
      <button type="submit" class="btn btn-primary w-100">Ver</button>
    </div>
  </form>

<?php if ($idh && $idt): ?>
  <canvas id="chartZona" style="max-height:400px;"></canvas>
  <script>
    const labels   = <?= json_encode(array_column($h24,'h')) ?>;   // eje X horas
    const dataHum  = <?= json_encode(array_column($h24,'humedad')) ?>;
    const dataTemp = <?= json_encode(array_column($t24,'temperatura')) ?>;

    new Chart(document.getElementById('chartZona').getContext('2d'),{
      type:'line',
      data:{
        labels:labels,
        datasets:[
          { label:'Humedad (%)',
            data:dataHum,
            borderColor:'#198754',
            backgroundColor:'rgba(25,135,84,0.15)',
            yAxisID:'yH',
            tension:0.3,
            fill:true },
          { label:'Temperatura (°C)',
            data:dataTemp,
            borderColor:'#0d6efd',
            backgroundColor:'rgba(13,110,253,0.15)',
            yAxisID:'yT',
            tension:0.3,
            fill:true }
        ]
      },
      options:{
        responsive:true,
        plugins:{ legend:{position:'top'} },
        scales:{
          yH:{ type:'linear', position:'left', suggestedMin:0, suggestedMax:100, title:{display:true,text:'% Humedad'} },
          yT:{ type:'linear', position:'right', suggestedMin:0, suggestedMax:50,  title:{display:true,text:'°C'} ,
               grid:{drawOnChartArea:false} }
        }
      }
    });
  </script>
<?php elseif ($idh || $idt): ?>
  <p class="text-danger">Selecciona <strong>ambos</strong> sensores para comparar.</p>
<?php endif; ?>
</main>

<?php include "../layout/footer.php"; ?>
</body></html>
