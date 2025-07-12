<?php
/* --------------------------------------------------------------------------
   Vista de lecturas de HUMEDAD
   - Si el id pertenece a un sensor de temperatura → redirige a temperatura
   - Muestra último valor en “gauge”
   - Historial de las últimas 24 h en un área/onda (Chart.js) + tabla
   -------------------------------------------------------------------------- */

include "controller_humedad.php";   // Carga helper + $pdo

$id_sensor = $_GET['id'] ?? null;
if (!$id_sensor) { echo "ID de sensor no especificado."; exit; }

/* ---- 1. Verificar tipo de sensor --------------------------------------- */
$tipo = $pdo->prepare("SELECT tipo FROM sensores WHERE id_sensor = ?");
$tipo->execute([$id_sensor]);
$tipo = $tipo->fetchColumn();

if ($tipo === 'T') {                       // si es temperatura → redirige
    header("Location: ../temperatura/lectura.php?id=".$id_sensor);
    exit;
}

/* ---- 2. Lecturas -------------------------------------------------------- */
$ultima     = obtenerUltimaLectura($id_sensor);            // 1 registro
$historico  = obtenerLecturasUltimasHoras($id_sensor, 24); // array con 'hora', 'humedad'
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lectura Sensor H<?= $id_sensor ?></title>

    <?php include "../layout/head.php"; ?>
    <link rel="stylesheet" href="<?= $URL ?>/humedad/public/styles_lectura.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include "../layout/header.php"; ?>

<main class="container my-5">
    <h1 class="text-center mb-4 h1Lectura">
        Última Lectura del Sensor ID: <?= $id_sensor ?>
    </h1>

<?php if (!$ultima): ?>
    <p class="text-center">Este sensor todavía no tiene lecturas de humedad.</p>

<?php else: ?>
    <!-- ---------------- Gauge ---------------- -->
    <div class="row justify-content-center mb-5">
        <div class="col-md-6 text-center lectura">
            <canvas id="gaugeChart"></canvas>
            <div class="lecturainfo">
                <h2 class="mt-4"><?= $ultima['humedad'] ?> %</h2>
                <p>
                    Último registro:
                    <?= date('H:i', strtotime($ultima['fecha_lectura'])) ?> hs –
                    <?= date('d/m/Y', strtotime($ultima['fecha_lectura'])) ?>
                </p>
            </div>
        </div>
    </div>

    <!-- ---------------- Historial ---------------- -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="text-center mb-4 h1Lectura">
                Histórico de Humedad (Últimas 24 h)
            </h2>
            <canvas id="lineChart"></canvas>
        </div>
    </div>

    <h3 class="mt-4">Tabla de lecturas (24 h)</h3>
    <table class="table table-sm">
        <thead>
            <tr><th>Hora</th><th>Humedad (%)</th></tr>
        </thead>
        <tbody>
        <?php foreach ($historico as $h): ?>
            <tr>
                <td><?= $h['hora'] ?></td>
                <td><?= $h['humedad'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</main>

<?php include "../layout/footer.php"; ?>

<?php if ($ultima): /* scripts solo si existe data */ ?>
<script>
/* ---------- Gauge ---------- */
const humedad = <?= $ultima['humedad'] ?>;
new Chart(document.getElementById('gaugeChart').getContext('2d'),{
    type:'doughnut',
    data:{
        labels:['Humedad','Restante'],
        datasets:[{
            data:[humedad, 100-humedad],
            backgroundColor:['#52c234','#e3c8b8'],
            borderWidth:0
        }]
    },
    options:{
        rotation:-90,
        circumference:180,
        cutout:'70%',
        plugins:{ legend:{display:false}, tooltip:{enabled:false} }
    }
});

/* ---------- Área / onda ---------- */
const hist     = <?= json_encode($historico) ?>; // [{hora,humedad}, …]
const labels   = hist.map(r => r.hora);
const datosHum = hist.map(r => r.humedad);

new Chart(document.getElementById('lineChart').getContext('2d'),{
    type:'line',
    data:{
        labels: labels,
        datasets:[{
            label:'Humedad (%)',
            data: datosHum,
            tension:0.4,
            fill:true
        }]
    },
    options:{
        plugins:{ legend:{display:false} },
        scales:{
            x:{ title:{display:true,text:'Hora'} },
            y:{ title:{display:true,text:'%'},
                suggestedMin:0, suggestedMax:100 }
        }
    }
});
</script>
<?php endif; ?>
</body>
</html>
