<?php
include "../app/config/config.php"; 

$humedad = rand(30, 70);
$id_sensor = rand(1, 2);

try {
    $stmt_check = $pdo->prepare("SELECT id_sensor FROM Sensores WHERE id_sensor = :id_sensor");
    $stmt_check->bindParam(':id_sensor', $id_sensor, PDO::PARAM_INT);
    $stmt_check->execute();
    
    if ($stmt_check->rowCount() == 0) {
        echo "Error: El sensor con ID $id_sensor no existe en la base de datos.";
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO lecturas (id_sensor, humedad, fecha_lectura) VALUES (:id_sensor, :humedad, NOW())");
    $stmt->bindParam(':id_sensor', $id_sensor, PDO::PARAM_INT);
    $stmt->bindParam(':humedad', $humedad, PDO::PARAM_STR);
    
    $stmt->execute();
    
    echo "Lectura insertada correctamente para el sensor $id_sensor: $humedad%";
} catch (PDOException $e) {
    echo "Error al insertar lectura: " . $e->getMessage();
}
?>
