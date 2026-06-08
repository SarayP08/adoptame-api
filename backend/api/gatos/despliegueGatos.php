<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once __DIR__ . '/../config/conexion.php';

$sql = "SELECT * FROM gatos";
$result = $conn->query($sql);

$gatos = [];

while ($row = $result->fetch_assoc()) {
    $gatos[] = $row;
}

echo json_encode($gatos);