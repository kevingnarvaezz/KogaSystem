CREATE TABLE tb_humedad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sensor_id INT NOT NULL,
    humedad DECIMAL(5, 2) NOT NULL, 
    fecha_hora DATETIME NOT NULL
);