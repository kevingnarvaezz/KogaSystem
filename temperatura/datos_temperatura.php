<?php
require_once 'controller_temperatura.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error'=>'POST only']); exit;
}

$idSensor = filter_input(INPUT_POST, 'sensor_id', FILTER_VALIDATE_INT);
$valor    = filter_input(INPUT_POST, 'valor',      FILTER_VALIDATE_FLOAT);

if (!$idSensor || $valor === false) {
    http_response_code(400);
    echo json_encode(['error'=>'Bad parameters']); exit;
}

if (registrarLecturaTemp($idSensor, $valor)) {
    http_response_code(201);
    echo json_encode(['status'=>'ok']);
} else {
    http_response_code(500);
    echo json_encode(['error'=>'DB error']);
}
