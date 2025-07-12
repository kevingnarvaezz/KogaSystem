<?php 
include "../app/config/config.php"; // Conexión a la base de datos

// Obtener todos los sensores
function obtenerSensores() {
    global $pdo;
    $query = "SELECT * FROM Sensores";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener lecturas de un sensor específico
function obtenerLecturasPorSensor($id_sensor) {
    global $pdo;
    $query = "SELECT * FROM Lecturas WHERE id_sensor = :id_sensor ORDER BY fecha_lectura DESC LIMIT 50";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_sensor', $id_sensor, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Crear nuevo sensor
function crearSensor($nombre, $descripcion) {
    global $pdo;
    $query = "INSERT INTO Sensores (nombre, descripcion, fecha_instalacion) VALUES (:nombre, :descripcion, NOW())";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
    return $stmt->execute();
}

// Editar sensor
function editarSensor($id_sensor, $nombre, $descripcion) {
    global $pdo;
    $query = "UPDATE Sensores SET nombre = :nombre, descripcion = :descripcion WHERE id_sensor = :id_sensor";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
    $stmt->bindParam(':id_sensor', $id_sensor, PDO::PARAM_INT);
    return $stmt->execute();
}

// Eliminar sensor
function eliminarSensor($id_sensor) {
    global $pdo;
    $query = "DELETE FROM Sensores WHERE id_sensor = :id_sensor";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_sensor', $id_sensor, PDO::PARAM_INT);
    return $stmt->execute();
}


function obtenerUltimaLectura($id_sensor) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM lecturas WHERE id_sensor = :id_sensor ORDER BY fecha_lectura DESC LIMIT 1");
    $stmt->bindParam(':id_sensor', $id_sensor, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function obtenerLecturasUltimasHoras($id_sensor) {
    global $pdo; // Utilizamos la conexión global establecida en config.php

    $horasAtras = 6; // Define cuántas horas atrás deseas consultar
    $sql = "SELECT 
                DATE_FORMAT(fecha_lectura, '%H:%i') AS hora, 
                humedad 
            FROM lecturas 
            WHERE id_sensor = :id_sensor 
              AND fecha_lectura >= DATE_SUB(NOW(), INTERVAL :horas HOUR)
            ORDER BY fecha_lectura ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_sensor', $id_sensor, PDO::PARAM_INT);
    $stmt->bindParam(':horas', $horasAtras, PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve las lecturas como un array asociativo
}

?>

