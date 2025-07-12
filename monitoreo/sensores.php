<?php
require_once '../app/config/config.php';

/* Iniciar sesión solo si no está activa */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id'])) {
    header('Location: ../login/login.php');
    exit;
}

/* Traer todos los sensores con su tipo */
$sql = "SELECT id_sensor, nombre, descripcion, tipo
        FROM sensores
        ORDER BY id_sensor";
$sensores = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Monitoreo de Sensores</title>
  <?php include "../layout/head.php"; ?>
  <style>
    .card-sensor{border:0;text-align:center}
    .card-sensor img{width:120px;margin:auto}
    .badge-temp{background:#0d6efd}  /* azul  */
    .badge-hum {background:#198754}  /* verde */
  </style>
</head>
<body>
<?php include "../layout/header.php"; ?>

<main class="container py-5">
  <h1 class="text-center mb-4">Listado de Sensores</h1>

  <!-- Botón para la vista combinada Humedad + Temperatura -->
  <div class="text-end mb-3">
    <a href="<?php echo $URL; ?>/monitoreo/zona.php" class="btn btn-outline-primary">
      Comparar Humedad + Temperatura
    </a>
  </div>

  <div class="row justify-content-center g-4">
  <?php foreach ($sensores as $s):
        /* URL según tipo */
        $url   = ($s['tipo'] === 'T')
               ? "../temperatura/lectura.php?id=".$s['id_sensor']
               : "../humedad/lectura.php?id=".$s['id_sensor'];
        $badge = $s['tipo'] === 'T' ? 'badge-temp' : 'badge-hum';
  ?>
    <div class="col-6 col-md-4 col-lg-3">
      <div class="card card-sensor p-3 shadow-sm">
        <a href="<?= $url ?>">
          <img src="../layout/img/Logo.png" alt="sensor">
          <h5 class="mt-2"><?= htmlspecialchars($s['nombre']) ?></h5>
          <small><?= htmlspecialchars($s['descripcion']) ?></small>
          <div class="badge <?= $badge ?> mt-2">
            <?= $s['tipo'] === 'T' ? 'Temperatura' : 'Humedad'; ?>
          </div>
        </a>
      </div>
    </div>
  <?php endforeach; ?>
  </div>
</main>

<?php include "../layout/footer.php"; ?>
</body>
</html>
