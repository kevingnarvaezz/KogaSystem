<?php
require 'config.php';
$stmt = $pdo->query("SELECT 1");
print_r($stmt->fetch());