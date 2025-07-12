<?php
require_once('../app/config/config.php');

/* ---------- Obtener usuario autenticado ---------- */
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$id_usuario = $_SESSION['id'] ?? 1;   // fallback por si no llega sesión

/* ---------- CREAR sensor ---------- */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear_sensor'])) {
    $nombre            = $_POST['nombre'];
    $descripcion       = $_POST['descripcion'];
    $fecha_instalacion = $_POST['fecha_instalacion'];
    $tipo              = $_POST['tipo'];   // 'H' o 'T'

    $sql  = "INSERT INTO sensores (nombre, descripcion, fecha_instalacion, tipo)
             VALUES (:nombre, :descripcion, :fecha_instalacion, :tipo)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre'            => $nombre,
        ':descripcion'       => $descripcion,
        ':fecha_instalacion' => $fecha_instalacion,
        ':tipo'              => $tipo
    ]);

    /* historial */
    $stmt_h = $pdo->prepare(
        "INSERT INTO historial_acceso (id_usuario, accion, fecha)
         VALUES (:uid, 'crear sensor', NOW())");
    $stmt_h->execute([':uid'=>$id_usuario]);
}

/* ---------- EDITAR sensor ---------- */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar_sensor'])) {
    $id_sensor         = $_POST['id_sensor'];
    $nombre            = $_POST['nombre'];
    $descripcion       = $_POST['descripcion'];
    $fecha_instalacion = $_POST['fecha_instalacion'];
    $tipo              = $_POST['tipo'];

    $sql  = "UPDATE sensores
             SET nombre = :nombre,
                 descripcion = :descripcion,
                 fecha_instalacion = :fecha_instalacion,
                 tipo = :tipo
             WHERE id_sensor = :id_sensor";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre'            => $nombre,
        ':descripcion'       => $descripcion,
        ':fecha_instalacion' => $fecha_instalacion,
        ':tipo'              => $tipo,
        ':id_sensor'         => $id_sensor
    ]);

    $stmt_h = $pdo->prepare(
        "INSERT INTO historial_acceso (id_usuario, accion, fecha)
         VALUES (:uid, 'editar sensor', NOW())");
    $stmt_h->execute([':uid'=>$id_usuario]);
}

/* ---------- ELIMINAR sensor ---------- */
if (isset($_GET['eliminar_sensor'])) {
    $id_sensor = $_GET['eliminar_sensor'];

    $pdo->prepare("DELETE FROM lecturas WHERE id_sensor = ?")->execute([$id_sensor]);
    $pdo->prepare("DELETE FROM lecturas_temp WHERE id_sensor = ?")->execute([$id_sensor]);
    $pdo->prepare("DELETE FROM sensores WHERE id_sensor = ?")->execute([$id_sensor]);

    $pdo->prepare(
        "INSERT INTO historial_acceso (id_usuario, accion, fecha)
         VALUES (:uid, 'eliminar sensor', NOW())")
        ->execute([':uid'=>$id_usuario]);
}

/* ---------- Obtener lista de sensores ---------- */
$sensores = $pdo->query("SELECT * FROM sensores ORDER BY id_sensor")
                ->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gestión de Sensores</title>
<?php include "../layout/head.php"; ?>
<link rel="stylesheet"
      href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
      integrity="sha384-9aIt2S5v5bI1M+M76xv7Hj7gfbl3hBPzzE8RggY7J4h54G7vhvLfwe8Yarbyl8l5D"
      crossorigin="anonymous">
<script>
function mostrarFormularioEdicion(id){
  document.getElementById('form_editar_'+id).style.display='block';
  document.getElementById('sensores_lista').style.display='none';
}
function cancelarEdicion(){
  document.getElementById('sensores_lista').style.display='block';
  document.querySelectorAll('.formulario_editar')
          .forEach(f=>f.style.display='none');
}
</script>
</head>
<body>
<?php include "../layout/header.php"; ?>

<main class="container mt-4">

<h1 class="text-center">Crear Nuevo Sensor</h1>

<!-- ---------- Form Alta ---------- -->
<div class="card mt-4">
  <div class="card-header"><h3>Crear Nuevo Sensor</h3></div>
  <div class="card-body">
    <form method="POST">
      <div class="form-group">
        <label>Nombre del Sensor:</label>
        <input type="text" name="nombre" class="form-control" required>
      </div>
      <div class="form-group">
        <label>Descripción:</label>
        <textarea name="descripcion" class="form-control" required></textarea>
      </div>
      <div class="form-group">
        <label>Fecha de Instalación:</label>
        <input type="date" name="fecha_instalacion" class="form-control" required>
      </div>
      <div class="form-group">
        <label>Tipo de Sensor:</label>
        <select name="tipo" class="form-control" required>
            <option value="H">Humedad</option>
            <option value="T">Temperatura</option>
        </select>
      </div>
      <button type="submit" name="crear_sensor" class="btn btn-primary">Crear Sensor</button>
    </form>
  </div>
</div>

<!-- ---------- Lista de Sensores ---------- -->
<div class="card mt-4" id="sensores_lista">
  <div class="card-header"><h3>Lista de Sensores</h3></div>
  <div class="card-body">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Nombre</th><th>Descripción</th><th>Fecha</th><th>Tipo</th><th>Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($sensores as $s): ?>
        <tr>
          <td><?= htmlspecialchars($s['nombre']) ?></td>
          <td><?= htmlspecialchars($s['descripcion']) ?></td>
          <td><?= $s['fecha_instalacion'] ?></td>
          <td><?= $s['tipo']=='T' ? 'Temperatura' : 'Humedad' ?></td>
          <td>
            <button class="btn btn-warning btn-sm"
                    onclick="mostrarFormularioEdicion(<?= $s['id_sensor'] ?>)">Editar</button>
            <a href="?eliminar_sensor=<?= $s['id_sensor'] ?>"
               class="btn btn-danger btn-sm"
               onclick="return confirm('¿Eliminar este sensor?')">Eliminar</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ---------- Formularios de Edición (uno por sensor) ---------- -->
<?php foreach ($sensores as $s): ?>
<div class="formulario_editar" id="form_editar_<?= $s['id_sensor'] ?>" style="display:none;">
  <div class="card mt-4">
    <div class="card-header"><h3>Editar Sensor: <?= htmlspecialchars($s['nombre']) ?></h3></div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="id_sensor" value="<?= $s['id_sensor'] ?>">
        <div class="form-group">
          <label>Nombre del Sensor:</label>
          <input type="text" name="nombre" class="form-control"
                 value="<?= htmlspecialchars($s['nombre']) ?>" required>
        </div>
        <div class="form-group">
          <label>Descripción:</label>
          <textarea name="descripcion" class="form-control" required><?= htmlspecialchars($s['descripcion']) ?></textarea>
        </div>
        <div class="form-group">
          <label>Fecha de Instalación:</label>
          <input type="date" name="fecha_instalacion" class="form-control"
                 value="<?= $s['fecha_instalacion'] ?>" required>
        </div>
        <div class="form-group">
          <label>Tipo de Sensor:</label>
          <select name="tipo" class="form-control" required>
              <option value="H" <?= $s['tipo']=='H'?'selected':'' ?>>Humedad</option>
              <option value="T" <?= $s['tipo']=='T'?'selected':'' ?>>Temperatura</option>
          </select>
        </div>
        <button type="submit" name="editar_sensor" class="btn btn-primary">Guardar Cambios</button>
        <button type="button" onclick="cancelarEdicion()" class="btn btn-secondary">Cancelar</button>
      </form>
    </div>
  </div>
</div>
<?php endforeach; ?>

</main>

<?php include "../layout/footer.php"; ?>
</body>
</html>
