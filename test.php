<?php
$configPath = __DIR__ . "/app/config/config.php";

if (file_exists($configPath)) {
    require_once $configPath;
    echo "Archivo de configuración cargado correctamente.<br>";
} else {
    die("Error: No se encontró el archivo de configuración.");
}
?>