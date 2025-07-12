<?php


    define('SERVIDOR', 'localhost');
    define('USUARIO', 'root');
    define('PASSWORD', 'mysql');

    define('BD', 'kogasystem');


$URL = 'http://localhost/kogasystem';

$servidor = "mysql:dbname=" . BD . ";host=" . SERVIDOR;

try {
    $pdo = new PDO($servidor, USUARIO, PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8"));
    //echo "Conexión exitosa en config.php.<br>"; // Agregar este mensaje temporalmente
} catch (PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
}