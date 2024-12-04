<?php

if (!defined('SERVIDOR')) {
    define('SERVIDOR', 'localhost');
}
if (!defined('USUARIO')) {
    define('USUARIO', 'root');
}
if (!defined('PASSWORD')) {
    define('PASSWORD', 'mysql');
}
if (!defined('BD')) {
    define('BD', 'kogasystem');
}

$URL = 'http://localhost/KogaSystem';

$servidor = "mysql:dbname=" . BD . ";host=" . SERVIDOR;

try {
    $pdo = new PDO($servidor, USUARIO, PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8"));
    // echo "<script>alert('La conexión a la base de datos fue exitosa.')</script>";
} catch (PDOException $e) {
    echo "<script>alert('Error en la conexión de la base de datos.')</script>";
}
