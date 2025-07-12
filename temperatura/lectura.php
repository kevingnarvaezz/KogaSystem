<?php
require_once 'controller_temperatura.php';

$idSensor = $_GET['id'] ?? null;
if (!$idSensor) { echo 'Sensor no especificado'; exit; }

/* ---------- 1. Verificar tipo y redirigir si es sensor de humedad ---------- */
$tipo = $pdo->prepare("SELECT tipo FROM sensores WHERE id_sensor = ?");
$tipo->execute([$idSensor]);
$tipo = $tipo->fetchColumn();

if ($tipo === 'H') {                      // si alguien forzó la URL equivocada
    header("Location: ../humedad/lectura.php?id=".$idSensor);
    exit;
}

/* ---------- 2. Obtener lecturas de temperatura ---------- */
$ultima    = obtenerUltimaLecturaTemp((int)$idSensor);
$historico = obtenerLecturasUltimasHorasTemp((int)$idSensor);
?>
<!DOCTYPE html><html lang="es"><head>
  <meta charset="utf-8">
  <title>Temperatura sensor <?= htmlspecialchars($idSensor) ?></title>
  <?php include "../layout/head.php"; ?>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head><body>
<?php include "../layout/header.php"; ?>

<main class="container py-4">
<?php if (!$ultima): ?>
  <h2>Aún no hay lecturas para este sensor</h2>
<?php else: ?>
  <h2>Última lectura: <?= $ultima['temperatura'] ?> °C
      <small class="text-muted">(<?= $ultima['fecha_lectura'] ?>)</small></h2>

  <canvas id="chartTemp" style="max-width:100%; height:300px;"></canvas>

  <script>
    const labels  = <?= json_encode(array_column($historico,'fecha_lectura')) ?>;
    const datos   = <?= json_encode(array_column($historico,'temperatura')) ?>;

    new Chart(document.getElementById('chartTemp').getContext('2d'),{
      type:'line',
      data:{ labels:labels,
             datasets:[{ label:'Temperatura (°C)',
                         data:datos, tension:0.3, fill:true }]},
      options:{ plugins:{ legend:{display:false}} }
    });
  </script>

  <h3 class="mt-4">Histórico (últimas 24 h)</h3>
  <table class="table table-sm">
    <?php foreach ($historico as $r): ?>
      <tr>
        <td><?= $r['fecha_lectura'] ?></td>
        <td><?= $r['temperatura'] ?> °C</td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>
</main>

<?php include "../layout/footer.php"; ?>
</body></html>
