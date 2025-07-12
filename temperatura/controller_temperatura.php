<?php
// Ajusta la ruta si tu config está en otra carpeta
require_once __DIR__.'/../app/config/config.php';

/* ---------- Sensores ---------- */
function obtenerSensoresTemp() {
    global $pdo;
    return $pdo->query("SELECT * FROM sensores")->fetchAll(PDO::FETCH_ASSOC);
}

/* ---------- Lecturas ---------- */
function registrarLecturaTemp(int $idSensor, float $valor): bool {
    global $pdo;
    $sql  = "INSERT INTO lecturas_temp (id_sensor, temperatura) VALUES (:id, :val)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([':id'=>$idSensor, ':val'=>$valor]);
}

function obtenerUltimaLecturaTemp(int $idSensor): ?array {
    global $pdo;
    $sql = "SELECT temperatura, fecha_lectura
            FROM lecturas_temp
            WHERE id_sensor = :id
            ORDER BY fecha_lectura DESC
            LIMIT 1";
    $st  = $pdo->prepare($sql);
    $st->execute([':id'=>$idSensor]);
    return $st->fetch(PDO::FETCH_ASSOC) ?: null;
}

function obtenerLecturasUltimasHorasTemp(int $idSensor, int $horas = 24): array {
    global $pdo;
    $sql = "SELECT temperatura, fecha_lectura
            FROM lecturas_temp
            WHERE id_sensor = :id
              AND fecha_lectura >= DATE_SUB(NOW(), INTERVAL :h HOUR)
            ORDER BY fecha_lectura ASC";
    $st  = $pdo->prepare($sql);
    $st->execute([':id'=>$idSensor, ':h'=>$horas]);
    return $st->fetchAll(PDO::FETCH_ASSOC);
}
