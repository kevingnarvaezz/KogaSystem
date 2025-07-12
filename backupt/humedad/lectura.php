<?php
include "controller_humedad.php";

$id_sensor = $_GET['id'] ?? null;
if (!$id_sensor) {
    echo "ID de sensor no especificado.";
    exit;
}

// Obtener datos del sensor
$ultima_lectura = obtenerUltimaLectura($id_sensor);
$lecturas_historicas = obtenerLecturasUltimasHoras($id_sensor);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Última Lectura del Sensor <?= $id_sensor ?></title>
    <?php include "../layout/head.php"; ?>
    <link rel="stylesheet" href="<?php echo $URL ?>/humedad/public/styles_lectura.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include "../layout/header.php"; ?>

    <main class="container my-5">
        <h1 class="text-center mb-4 h1Lectura">Última Lectura del Sensor ID: <?= $id_sensor ?></h1>

        <div class="row justify-content-center mb-5">
            <div class="col-md-6 text-center lectura">
                <canvas id="gaugeChart"></canvas>
                <div class="lecturainfo">
                    <h2 class="mt-4"><?= $ultima_lectura['humedad'] ?>%</h2>
                    <p>Último registro: <?= date("H:i", strtotime($ultima_lectura['fecha_lectura'])) ?>hs – <?= date("d/m/Y", strtotime($ultima_lectura['fecha_lectura'])) ?></p>
                </div>
            </div>
        </div>

        <!-- Gráfico de línea -->
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="text-center mb-4 h1Lectura">Histórico de Humedad (Últimas Horas)</h2>
                <canvas id="lineChart"></canvas>
            </div>
        </div>
    </main>

    <?php include "../layout/footer.php"; ?>

    <script>
        // Datos para el gráfico de indicador
        const ctxGauge = document.getElementById('gaugeChart').getContext('2d');
        const humedad = <?= $ultima_lectura['humedad'] ?>;

        new Chart(ctxGauge, {
            type: 'doughnut',
            data: {
                labels: ['Humedad', 'Restante'],
                datasets: [{
                    data: [humedad, 100 - humedad],
                    backgroundColor: ['#52c234', '#e3c8b8'],
                    borderWidth: 0,
                }]
            },
            options: {
                rotation: -90,
                circumference: 180,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false },
                }
            }
        });

        // Datos dinámicos para el gráfico de línea
        const lecturas = <?= json_encode($lecturas_historicas) ?>;
        const labels = lecturas.map(item => item.hora); // Horas
        const datosHumedad = lecturas.map(item => item.humedad); // Humedad

        const ctxLine = document.getElementById('lineChart').getContext('2d');

        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Humedad',
                    data: datosHumedad,
                    borderColor: '#52c234',
                    backgroundColor: 'rgba(226, 178, 74, 0.2)',
                    fill: true,
                    tension: 0.4, // Suavizado de la curva
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Hora'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Humedad (%)'
                        },
                        suggestedMin: 0,
                        suggestedMax: 100
                    }
                }
            }
        });
    </script>
</body>

</html>
