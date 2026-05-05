<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}
$host = "localhost";
$user = "root";
$pass = "123";
$db   = "pawtita";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(["error" => $conn->connect_error]));
}

$conn->set_charset("utf8mb4");

$sql = "SELECT * FROM gatos";
$result = $conn->query($sql);

$gatos = [];

while ($row = $result->fetch_assoc()) {
    $gatos[] = $row;
}

echo json_encode($gatos);